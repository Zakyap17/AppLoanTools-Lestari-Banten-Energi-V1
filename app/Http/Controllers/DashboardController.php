<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function clearNotifications(Request $request)
    {
        $type = $request->input('type', 'all');
        $ids = $request->input('ids', []);
        
        $dismissed = session()->get('dismissed_notifications', []);
        
        if ($type === 'all') {
            foreach (['overdue', 'low_stock', 'calibration'] as $cat) {
                if (isset($ids[$cat])) {
                    $dismissed[$cat] = array_unique(array_merge($dismissed[$cat] ?? [], $ids[$cat]));
                }
            }
        }
        
        session()->put('dismissed_notifications', $dismissed);
        
        return response()->json(['success' => true]);
    }

    public function index()
    {
        // Fetch pending loans (status = 'ongoing')
        // Grouped by loan_code to prevent duplicate entries for multi-item loans
        $pendingLoans = \App\Models\Loan::with(['borrower', 'tool'])
            ->where('status', 'ongoing')
            ->orderBy('loan_date', 'desc')
            ->limit(50) // Fetch more to ensure we have enough unique codes
            ->get()
            ->unique('loan_code')
            ->take(10) // Display top 10 unique loans
            ->map(function ($loan) {
                // Calculate days since loan (Elapsed)
                $loan->days_pending = Carbon::parse($loan->loan_date)->startOfDay()->diffInDays(Carbon::now()->startOfDay());

                // Calculate days remaining (Countdown)
                if ($loan->expected_return_date) {
                    $dueDate = Carbon::parse($loan->expected_return_date)->startOfDay();
                    $loan->days_remaining = (int) floor(Carbon::now()->startOfDay()->diffInDays($dueDate, false));
                } else {
                    $loan->days_remaining = null; // No due date
                }
                return $loan;
            });

        // KPI Calculations
        $totalAssets = \App\Models\ToolItem::where('status', '!=', 'lost')->count(); // Exclude lost items from Total Inventory
        $onLoan = \App\Models\Loan::where('status', 'ongoing')->sum('loan_quantity');

        // In Repair = Damage + Open + Repairable (Logical Fix)
        $inRepair = \App\Models\DamageLost::where('type', 'damage')
            ->where('status', 'open')
            ->where('is_repairable', true)
            ->sum('quantity');

        // Available = Total - (On Loan + In Repair)
        $unavailableCount = $onLoan + $inRepair;
        $available = $totalAssets - $unavailableCount;
        if ($available < 0)
            $available = 0; // Prevent negative if data inconsistency

        $pendingReturnsCount = \App\Models\Loan::where('status', 'ongoing')->count();

        // Logic Update: Split Damage & Unrepairable
        $damageCount = \App\Models\DamageLost::where('status', 'open')
            ->where('type', 'damage')
            ->where('is_repairable', true)
            ->count();

        $unrepairableCount = \App\Models\DamageLost::where('status', 'open')
            ->where('type', 'damage')
            ->where('is_repairable', false)
            ->count();

        $lostCount = \App\Models\DamageLost::where('status', 'open')->where('type', 'lost')->count();

        // Total Attention Needed = Repairable Dmg + Lost + Unrepairable
        $damageLostCount = $damageCount + $lostCount + $unrepairableCount;

        // Calibration Due
        // Include OVERDUE items (date < now) and COMING SOON items (date <= now + 14)
        $calibrationDueCount = \App\Models\Tool::whereNotNull('next_calibration_date')
            ->where('next_calibration_date', '<=', Carbon::now()->addDays(14))
            ->count();

        // Fetch tools needing calibration for table display
        $calibrationDueTools = \App\Models\Tool::with(['manufacturer'])
            ->whereNotNull('next_calibration_date')
            ->where('next_calibration_date', '<=', Carbon::now()->addDays(14))
            ->orderBy('next_calibration_date', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($tool) {
                $now = Carbon::now();
                $dueDate = Carbon::parse($tool->next_calibration_date);

                // Calculate days remaining (negative if overdue, positive if upcoming)
                if ($dueDate->isPast()) {
                    $tool->days_remaining = -1 * (int) floor($now->diffInDays($dueDate, false));
                } else {
                    $tool->days_remaining = (int) floor($dueDate->diffInDays($now, false));
                }

                return $tool;
            });

        // Fetch Items In Service (Damage & Repairable & Open)
        $itemsInService = \App\Models\DamageLost::with(['tool', 'item'])
            ->where('type', 'damage')
            ->where('status', 'open')
            ->where('is_repairable', 1)
            ->orderBy('report_date', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.dashboard', compact(
            'pendingLoans',
            'itemsInService',
            'totalAssets',
            'onLoan',
            'available',
            'pendingReturnsCount',
            'damageLostCount',
            'damageCount',
            'unrepairableCount',
            'lostCount',
            'calibrationDueCount',
            'calibrationDueTools'
        ));
    }
}
