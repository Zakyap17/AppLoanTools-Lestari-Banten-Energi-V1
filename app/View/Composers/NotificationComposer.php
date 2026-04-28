<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Loan;
use App\Models\Tool;

class NotificationComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $dismissed = session()->get('dismissed_notifications', []);

        // 1. Overdue Loans (Ongoing & Return Date < Now)
        $overdueQuery = Loan::with(['tool', 'borrower'])
            ->where('status', 'ongoing')
            ->whereDate('expected_return_date', '<', now());

        if (!empty($dismissed['overdue'])) {
            $overdueQuery->whereNotIn('id', $dismissed['overdue']);
        }

        $overdueLoans = (clone $overdueQuery)->latest()->take(5)->get();
        $overdueCount = $overdueQuery->count();

        // 2. Out of Stock (Quantity == 0)
        $lowStockQuery = Tool::with('location')
            ->where('quantity', 0);

        if (!empty($dismissed['low_stock'])) {
            $lowStockQuery->whereNotIn('id', $dismissed['low_stock']);
        }

        $lowStockTools = (clone $lowStockQuery)->orderBy('quantity', 'asc')->take(5)->get();
        $lowStockCount = $lowStockQuery->count();

        // 3. Calibration Needed (H-30)
        $today = \Carbon\Carbon::now()->startOfDay();
        $targetDate = $today->copy()->addDays(30);

        $calibrationQuery = Tool::whereNotNull('next_calibration_date')
            ->whereDate('next_calibration_date', '<=', $targetDate);

        if (!empty($dismissed['calibration'])) {
            $calibrationQuery->whereNotIn('id', $dismissed['calibration']);
        }

        $calibrationTools = (clone $calibrationQuery)->orderBy('next_calibration_date', 'asc')->take(5)->get();
        $calibrationCount = $calibrationQuery->count();

        $data = [
            'notifOverdueLoans' => $overdueLoans,
            'notifOverdueCount' => $overdueCount,
            'notifLowStockTools' => $lowStockTools,
            'notifLowStockCount' => $lowStockCount,
            'notifCalibrationTools' => $calibrationTools,
            'notifCalibrationCount' => $calibrationCount,
            'totalNotifications' => $overdueCount + $lowStockCount + $calibrationCount
        ];

        $view->with($data);
    }
}
