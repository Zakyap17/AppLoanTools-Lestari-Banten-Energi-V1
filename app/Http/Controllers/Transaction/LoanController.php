<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoanCreated;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        // 1. Get Unique Loan Codes (Paginated)
        $query = \App\Models\Loan::query()->select('loan_code')->distinct();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('loan_code', 'ilike', "%{$search}%")
                ->orWhereHas('borrower', function ($q) use ($search) {
                    $q->where('name', 'ilike', "%{$search}%");
                });
        }

        $codes = $query->latest('loan_code')->paginate(10)->withQueryString();

        // 2. Fetch All Loan Items matching these codes
        $allLoans = \App\Models\Loan::with(['tool' => function($q) { $q->withTrashed()->with(['location', 'rack']); }, 'borrower', 'keeper', 'item'])
            ->whereIn('loan_code', $codes->pluck('loan_code'))
            ->get()
            ->groupBy('loan_code');

        // 3. Attach the grouped items to the paginated result (manually or just pass both)
        // A cleaner way is to map the paginated items to the groups
        $loans = $codes; // Keep structure for pagination links

        // KPI Calculations
        $activeLoanDocs = \App\Models\Loan::where('status', 'ongoing')->distinct('loan_code')->count('loan_code');
        $itemsOnLoan = \App\Models\Loan::where('status', 'ongoing')->sum('loan_quantity');

        return view('transaction.loan.index', compact('loans', 'allLoans', 'activeLoanDocs', 'itemsOnLoan'));
    }

    public function create()
    {
        $employees = \App\Models\Employee::orderBy('name')->get();

        // Pass empty tools collection for AJAX
        $tools = collect();

        // Auto-detect Keeper based on currently logged-in user
        $user = auth()->user();
        $detectedKeeperId = null;

        if ($user) {
            $employee = \App\Models\Employee::where('nik', $user->username)
                ->orWhere('name', 'ilike', $user->name)
                ->first();
            
            if ($employee) {
                $detectedKeeperId = $employee->id;
            }
        }

        // Generate Doc No: LOAN-YYYYMMDD-XXXX
        $count = \App\Models\Loan::whereDate('created_at', today())->count() + 1;
        $docNo = 'LOAN-' . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        return view('transaction.loan.create', compact('employees', 'docNo', 'tools', 'detectedKeeperId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'loan_code' => 'required',
            'loan_date' => 'required|date',
            'expected_return_date' => 'nullable|date|after_or_equal:loan_date',
            'employee_id' => 'required|exists:employees,id',
            'keeper_id' => 'required|exists:employees,id',
            'items' => 'required|array|min:1',
            'items.*.tool_id' => 'required|exists:tools,id',
            'items.*.tool_item_ids' => 'required|array',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $borrower = \App\Models\Employee::find($request->employee_id);
            $loanDetailsItems = [];

            $allToolItemIds = [];
            $loansToInsert = [];
            $timestamp = now();

            // 1. Initial Scan & Validation
            foreach ($request->items as $itemBlock) {
                $toolId = $itemBlock['tool_id'];
                $itemIds = $itemBlock['tool_item_ids'];

                // Collect IDs for bulk status check
                $allToolItemIds = array_merge($allToolItemIds, $itemIds);

                // Fetch tool info for Email (optimized later?)
                $tool = \App\Models\Tool::find($toolId);
                // Note: We'll handle quantity decrement separately strictly to be safe

                // Prepare Loan Records
                foreach ($itemIds as $itemId) {
                    $loansToInsert[] = [
                        'loan_code' => $request->loan_code,
                        'loan_date' => $request->loan_date,
                        'expected_return_date' => $request->expected_return_date,
                        'employee_id' => $request->employee_id,
                        'keeper_id' => $request->keeper_id,
                        'tool_id' => $toolId,
                        'tool_item_id' => $itemId,
                        'loan_quantity' => 1,
                        'notes' => $itemBlock['notes'] ?? null,
                        'status' => 'ongoing',
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                }

                // Prepare Email Data
                $loanDetailsItems[] = [
                    'tool_name' => $tool->name,
                    'tool_code' => $tool->code,
                    'quantity' => count($itemIds),
                    'serials' => \App\Models\ToolItem::whereIn('id', $itemIds)->pluck('serial_number')->toArray()
                ];
            }

            // 2. Bulk Check Availability (1 Query)
            $unavailableCount = \App\Models\ToolItem::whereIn('id', $allToolItemIds)
                ->where('status', '!=', 'available')
                ->count();

            if ($unavailableCount > 0) {
                throw new \Exception("One or more selected items are not available.");
            }

            // 3. Bulk Insert Loans (1 Query)
            if (!empty($loansToInsert)) {
                \App\Models\Loan::insert($loansToInsert);
            }

            // 4. Bulk Update Tool Items Status (1 Query)
            \App\Models\ToolItem::whereIn('id', $allToolItemIds)->update([
                'status' => 'in_use',
                'updated_at' => $timestamp
            ]);

            // 5. Update Tool Quantities (Loop Tools - unavoidable but fewer queries)
            foreach ($request->items as $itemBlock) {
                $tool = \App\Models\Tool::find($itemBlock['tool_id']);
                $qty = count($itemBlock['tool_item_ids']);
                if ($tool->quantity >= $qty) {
                    $tool->decrement('quantity', $qty);
                }
                // Check if empty
                if ($tool->fresh()->quantity == 0) {
                    $tool->update(['status' => 'in_use']);
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            // Send Email (Queued)
            if ($borrower && $borrower->email) {
                try {
                    $mail = Mail::to($borrower->email);

                    // Add Management BCC
                    $mgmtEmails = env('MANAGEMENT_EMAILS');
                    if ($mgmtEmails) {
                        $emails = array_map('trim', explode(',', $mgmtEmails));
                        $mail->bcc($emails);
                    }

                    $mail->queue(new LoanCreated([
                        'loan_code' => $request->loan_code,
                        'employee_name' => $borrower->name,
                        'loan_date' => $request->loan_date,
                        'expected_return_date' => $request->expected_return_date,
                        'notes' => $request->items[0]['notes'] ?? null,
                        'items' => $loanDetailsItems,
                    ]));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to queue loan email: " . $e->getMessage());
                }
            }

            return redirect()->route('loan.index')->with('success', 'Loan transaction created successfully.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create loan: ' . $e->getMessage())->withInput();
        }
    }

    public function searchAsset(Request $request)
    {
        $search = $request->get('q');
        \Illuminate\Support\Facades\Log::info('Asset search query: ' . $search);

        if (strlen($search) < 3) {
            return response()->json(['results' => []]);
        }

        $term = '%' . $search . '%';

        // Using lowercase 'ilike' for consistency with other working controllers
        $query = \App\Models\Tool::query();

        if ($request->has('include_trashed')) {
            $query->withTrashed()->with(['items' => function ($q) {
                $q->withTrashed();
            }]);
        } else {
            $query->with(['items' => function ($q) {
                $q->where('status', 'available');
            }]);
        }

        $tools = $query->where(function ($q) use ($term, $request) {
                $q->where('code', 'ilike', $term)
                    ->orWhere('name', 'ilike', $term)
                    ->orWhereHas('items', function ($sub) use ($term, $request) {
                        if ($request->has('include_trashed')) {
                            $sub->withTrashed();
                        }
                        $sub->where('serial_number', 'ilike', $term);
                    });
            })
            ->take(30)
            ->get();

        \Illuminate\Support\Facades\Log::info('Asset search results count: ' . $tools->count());

        $results = $tools->map(function ($tool) {
            return [
                'id' => $tool->id,
                'text' => $tool->code . ' - ' . $tool->name . ' (Stock: ' . ($tool->quantity ?? 0) . ')',
                'code' => $tool->code ?? '',
                'name' => $tool->name ?? '',
                'qty' => $tool->quantity ?? 0,
                'items' => $tool->items
            ];
        });

        return response()->json([
            'results' => $results
        ]);
    }


    public function getAvailableItems($toolId)
    {
        // Cache ID based on tool, 30 seconds duration should be enough to speed up UI
        // while keeping data relatively fresh.
        $cacheKey = "available_items_{$toolId}";

        $items = \Illuminate\Support\Facades\Cache::remember($cacheKey, 30, function () use ($toolId) {
            return \App\Models\ToolItem::withCount('loans')
                ->where('tool_id', $toolId)
                ->where('status', 'available')
                ->orderBy('loans_count', 'desc') // Frequently used first
                ->orderBy('serial_number')      // Then by serial
                ->take(500)
                ->get(['id', 'serial_number', 'condition']);
        });

        return response()->json($items);
    }

    public function processReturn(Request $request, $id)
    {
        $request->validate([
            'return_type' => 'required|in:good,damage,lost',
            'return_quantity' => 'nullable|integer|min:1',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $primaryLoan = \App\Models\Loan::findOrFail($id);

            // Check if primary loan is valid
            if ($primaryLoan->status !== 'ongoing') {
                return redirect()->back()->with('error', 'Selected item is already processed.');
            }

            // If quantity not provided from request, use the current loan's quantity
            $requestedQty = $request->return_quantity ?? $primaryLoan->loan_quantity;

            // Since we are now focusing on a specific Loan ID (row), 
            // the logic is simplified to just process this specific row.
            $loan = $primaryLoan;
            $takeFromRow = min($loan->loan_quantity, $requestedQty);

            // Determine item to update
            $tool = \App\Models\Tool::findOrFail($loan->tool_id);

            // Decrement loan quantity
            $loan->decrement('loan_quantity', $takeFromRow);
            $loan->refresh();

            // If row is fully returned, update status
            if ($loan->loan_quantity <= 0) {
                $newStatus = 'returned';
                if ($request->return_type === 'damage') {
                    $newStatus = 'returned_damage';
                } elseif ($request->return_type === 'lost') {
                    $newStatus = 'returned_lost';
                }

                $loan->update([
                    'status' => $newStatus,
                    'actual_return_date' => now(),
                    'loan_quantity' => 0
                ]);
            }

            if ($request->return_type === 'good') {
                // Restore stock
                $tool->increment('quantity', $takeFromRow);

                if ($tool->status !== 'available') {
                    $tool->update(['status' => 'available']);
                }

                if ($loan->tool_item_id && $takeFromRow > 0) {
                    \App\Models\ToolItem::where('id', $loan->tool_item_id)->update([
                        'status' => 'available',
                        'condition' => 'Good'
                    ]);
                }

            } else {
                // Damage or Lost
                $type = $request->return_type;
                $count = \App\Models\DamageLost::whereDate('created_at', today())->count() + 1;
                $reportCode = strtoupper($type) . '-' . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

                \App\Models\DamageLost::create([
                    'report_code' => $reportCode,
                    'reference_document' => $loan->loan_code,
                    'tool_id' => $tool->id,
                    'tool_item_id' => $loan->tool_item_id,
                    'employee_id' => $loan->employee_id,
                    'report_date' => now(),
                    'type' => $type,
                    'quantity' => $takeFromRow,
                    'severity' => $type === 'damage' ? 'medium' : 'high',
                    'description' => $request->reason ?? 'Reported from Loan Return: ' . $loan->loan_code,
                    'status' => 'open',
                ]);

                if ($loan->tool_item_id && $takeFromRow > 0) {
                    \App\Models\ToolItem::where('id', $loan->tool_item_id)->update([
                        'status' => $type,
                        'condition' => ucfirst($type)
                    ]);
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            $msg = $takeFromRow . ' item(s) processed as ' . ucfirst($request->return_type);

            // Preserve search/page state and keep the current loan row expanded
            return redirect()->route('loan.index', [
                'search' => $request->search,
                'page' => $request->page,
                'expanded' => $loan->loan_code
            ])->with('success', $msg);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Failed to process return: ' . $e->getMessage());
        }
    }

    public function print($loanCode)
    {
        $loans = \App\Models\Loan::with(['tool' => function($q) { $q->withTrashed(); }, 'borrower', 'keeper', 'item'])
            ->where('loan_code', $loanCode)
            ->get();

        if ($loans->isEmpty()) {
            return abort(404, 'Loan not found');
        }

        $firstLoan = $loans->first();

        return view('transaction.loan.print', compact('loans', 'firstLoan'));
    }
}
