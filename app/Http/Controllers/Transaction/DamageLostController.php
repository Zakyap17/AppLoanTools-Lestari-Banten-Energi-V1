<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DamageLostController extends Controller
{
    public function index(Request $request)
    {
        // Fetch One Representative Record per Report Code (Group by Document)
        // using Subquery to get MAX(id) for each report_code
        $latestIds = \App\Models\DamageLost::selectRaw('MAX(id) as id')
            ->groupBy('report_code')
            ->pluck('id');

        $query = \App\Models\DamageLost::with(['tool' => function($q) { $q->withTrashed()->with(['location', 'rack']); }, 'employee', 'item'])
            ->whereIn('id', $latestIds);

        // Search Filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('report_code', 'LIKE', "%$search%")
                    ->orWhere('description', 'LIKE', "%$search%")
                    ->orWhereHas('employee', function ($sub) use ($search) {
                        $sub->where('name', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('tool', function ($sub) use ($search) {
                        $sub->where('name', 'LIKE', "%$search%")
                            ->orWhere('code', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('item', function ($sub) use ($search) {
                        $sub->where('serial_number', 'LIKE', "%$search%");
                    });
            });
        }

        $reports = $query->latest()->paginate(10);

        // Fetch Only Available Tool Items for Direct Reporting (Manual)
        // Eager load nested relationships for the new UI (Location, Rack, Group, Type)
        $tools = \App\Models\ToolItem::with(['tool.location', 'tool.rack', 'tool.subGroup', 'tool.type'])
            ->whereIn('status', ['available', 'in_use', 'damage'])
            ->get();

        // Note: variable name $tools is kept to minimize view changes, but it now contains ToolItems
        $employees = \App\Models\Employee::orderBy('name')->get();

        // 1. Fetch Active Loans for the Dropdown
        // 1. Fetch Active Loans for the Dropdown
        $activeLoans = \App\Models\Loan::whereIn('status', ['ongoing', 'overdue'])
            ->select('loan_code')
            ->distinct()
            ->orderBy('loan_code', 'desc')
            ->get();

        // Fetch IDs of items that have OPEN damage reports
        $openDamageItemIds = \App\Models\DamageLost::where('status', 'open')
            ->whereNotNull('tool_item_id')
            ->pluck('tool_item_id')
            ->toArray();

        // KPI Counts (Server Side for Accuracy) - Exclude Unrepairable from "Open" count
        $openDamageCount = \App\Models\DamageLost::where('type', 'damage')
            ->where('status', 'open')
            ->where('is_repairable', true) // Only count repairable items
            ->count();

        $lostCount = \App\Models\DamageLost::where('type', 'lost')->count();

        return view('transaction.damagelost.index', compact('reports', 'tools', 'employees', 'activeLoans', 'openDamageItemIds', 'openDamageCount', 'lostCount'));
    }

    public function getLoanDetails($loanCode)
    {
        // 1. Get the loan records for this code
        $loans = \App\Models\Loan::with(['tool' => function($q) { $q->withTrashed(); }, 'borrower', 'item']) // Loading 'item' (ToolItem)
            ->where('loan_code', $loanCode)
            ->whereIn('status', ['ongoing', 'overdue'])
            ->get();

        if ($loans->isEmpty()) {
            return response()->json(['error' => 'Loan not found or no active items'], 404);
        }

        // 2. Extract Borrower (taking from first item)
        $borrower = $loans->first()->borrower;

        // 3. Map tools with Item Serial
        $tools = $loans->map(function ($loan) {
            return [
                'id' => $loan->tool->id,
                'name' => $loan->tool->name,
                'code' => $loan->tool->code,
                'tool_item_id' => $loan->tool_item_id, // Specific Item ID
                'serial_number' => $loan->item ? $loan->item->serial_number : 'Qty: ' . $loan->loan_quantity,
                'loan_quantity' => $loan->loan_quantity
            ];
        });

        return response()->json([
            'borrower_id' => $borrower->id,
            'tools' => $tools
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'report_date' => 'required|date',
            'type' => 'required|in:damage,lost',
            'severity' => 'required_if:type,damage|in:low,medium,high',
            'description' => 'nullable|string',
            'is_repairable' => 'boolean',
            'loan_doc' => 'nullable|string',
            'items_json' => 'nullable|string', // JSON from new UI
            // Legacy/Fallback
            'tool_item_id' => 'nullable|array',
            'tool_item_id.*' => 'exists:tool_items,id',
            'tool_id' => 'required_without_all:tool_item_id,items_json|exists:tools,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Generate Code ONCE for the batch: [TYPE]-[YYYYMMDD]-[SEQ]
            $typePrefix = strtoupper($request->type);
            $dateCode = date('Ymd');
            $lastReport = \App\Models\DamageLost::where('report_code', 'like', "{$typePrefix}-{$dateCode}-%")
                ->orderBy('id', 'desc')
                ->first();

            $nextSeq = 1;
            if ($lastReport) {
                $parts = explode('-', $lastReport->report_code);
                $lastSeq = end($parts);
                if (is_numeric($lastSeq)) {
                    $nextSeq = (int) $lastSeq + 1;
                }
            }
            $reportCode = sprintf("%s-%s-%04d", $typePrefix, $dateCode, $nextSeq);

            // Determine Items to Process
            $itemsJson = $request->input('items_json');
            $itemsToProcess = [];

            if ($itemsJson) {
                // NEW JSON MODE
                $decoded = json_decode($itemsJson, true);
                if (is_array($decoded)) {
                    foreach ($decoded as $item) {
                        $itemsToProcess[] = [
                            'tool_item_id' => $item['tool_item_id'],
                            'quantity' => $item['quantity'] ?? 1, // Usually 1 for serialized
                            'notes' => $item['notes'] ?? ''
                        ];
                    }
                }
            } elseif ($request->has('tool_item_id')) {
                // LEGACY MULTI-ITEM MODE
                foreach ($request->input('tool_item_id', []) as $id) {
                    $itemsToProcess[] = [
                        'tool_item_id' => $id,
                        'quantity' => 1,
                        'notes' => ''
                    ];
                }
            }

            // Force is_repairable to FALSE if type is 'lost'
            $isRepairable = ($request->type === 'lost') ? false : $request->boolean('is_repairable');

            // Default severity for lost items (if not provided)
            $severity = $request->severity;
            if ($request->type === 'lost') {
                $severity = 'high';
            }

            if (!empty($itemsToProcess)) {
                // MULTI-ITEM MODE (Serialized)
                foreach ($itemsToProcess as $data) {
                    $itemId = $data['tool_item_id'];
                    $toolItem = \App\Models\ToolItem::findOrFail($itemId);
                    $tool = $toolItem->tool;

                    // Create Report
                    \App\Models\DamageLost::create([
                        'report_code' => $reportCode, // Shared Code
                        'reference_document' => $request->loan_doc,
                        'tool_id' => $tool->id, // Derived from item
                        'tool_item_id' => $itemId,
                        'employee_id' => $request->employee_id,
                        'report_date' => $request->report_date,
                        'type' => $request->type,
                        'quantity' => 1, // Always 1 for serialized
                        'severity' => $severity,
                        'description' => $request->description . ($data['notes'] ? " [Note: {$data['notes']}]" : ""),
                        'is_repairable' => $isRepairable,
                        'status' => 'open'
                    ]);

                    // Deduct Stock (Sync)
                    $tool->decrement('quantity', 1);

                    // Update Status
                    $toolItem->update(['status' => $request->type]);

                    // Close Loan if exists
                    $activeLoan = \App\Models\Loan::where('tool_item_id', $itemId)
                        ->whereIn('status', ['ongoing', 'overdue'])
                        ->first();

                    if ($activeLoan) {
                        $newStatus = 'returned';
                        if ($request->type === 'damage') {
                            $newStatus = 'returned_damage';
                        } elseif ($request->type === 'lost') {
                            $newStatus = 'returned_lost';
                        }

                        $activeLoan->update([
                            'status' => $newStatus,
                            'actual_return_date' => now(),
                            'notes' => $activeLoan->notes . " [Reported {$request->type}]"
                        ]);
                    }
                }
            } else {
                // GENERIC TOOL MODE (Single)
                $tool = \App\Models\Tool::findOrFail($request->tool_id);

                if ($tool->quantity < $request->quantity) {
                    // Check stock
                    throw new \Exception("Insufficient stock for {$tool->name}");
                }

                \App\Models\DamageLost::create([
                    'report_code' => $reportCode,
                    'reference_document' => $request->loan_doc,
                    'tool_id' => $request->tool_id,
                    'tool_item_id' => null,
                    'employee_id' => $request->employee_id,
                    'report_date' => $request->report_date,
                    'type' => $request->type,
                    'quantity' => $request->quantity,
                    'severity' => $severity,
                    'description' => $request->description,
                    'is_repairable' => $isRepairable,
                    'status' => 'open'
                ]);

                $tool->decrement('quantity', $request->quantity);
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('damage-lost.index')->with('success', 'Report created successfully.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create report: ' . $e->getMessage());
        }
    }

    public function print(Request $request, $id)
    {
        // Fetch the requested report
        $report = \App\Models\DamageLost::with(['tool' => function($q) { $q->withTrashed()->with(['location', 'rack']); }, 'employee'])->findOrFail($id);

        // Fetch ALL reports sharing the same report_code (Siblings for Multi-Item)
        $reports = \App\Models\DamageLost::with(['tool' => function($q) { $q->withTrashed()->with(['location', 'rack']); }, 'employee', 'item'])
            ->where('report_code', $report->report_code)
            ->get();

        $mode = $request->query('mode', 'print'); // Default to 'print' if not set
        return view('transaction.damagelost.print_template', compact('report', 'reports', 'mode'));
    }

    public function resolve(Request $request, $id)
    {
        $request->validate([
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240', // 10MB Limit
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $report = \App\Models\DamageLost::findOrFail($id);
            if ($report->status !== 'open') {
                return redirect()->back()->with('error', 'Report is already resolved.');
            }

            if ($report->type !== 'damage') {
                return redirect()->back()->with('error', 'Only damaged items can be repaired.');
            }

            $tool = \App\Models\Tool::findOrFail($report->tool_id);

            // Restore stock
            $tool->increment('quantity', $report->quantity);

            // Update tool status back to available if it was restricted
            if ($tool->status === 'damage' || $tool->status === 'in_use') {
                $tool->update(['status' => 'available']);
            }

            // Also update ToolItem status if exists
            if ($report->tool_item_id) {
                $toolItem = \App\Models\ToolItem::find($report->tool_item_id);
                if ($toolItem) {
                    $toolItem->update([
                        'status' => 'available',
                        'condition' => 'Good'
                    ]);
                }
            }

            $evidencePath = null;
            if ($request->hasFile('evidence')) {
                $evidencePath = $request->file('evidence')->store('evidence', 'public');
            }

            // Update report status
            $report->update([
                'status' => 'resolved',
                'resolution_evidence' => $evidencePath
            ]);

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->back()->with('success', 'Tool repaired successfully and stock restored to Available.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Failed to resolve: ' . $e->getMessage());
        }
    }
}
