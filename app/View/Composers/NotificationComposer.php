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
        // Cache Key based on User ID to ensure personal notifications (if any) or Global
        // Since these are global queries (not user specific yet), we use a global key.
        // If they become user-specific, append auth()->id().
        $cacheKey = 'topbar_notifications';

        $data = \Illuminate\Support\Facades\Cache::rememberForever($cacheKey, function () {
            // 1. Overdue Loans (Ongoing & Return Date < Now)
            $overdueLoans = Loan::with(['tool', 'borrower'])
                ->where('status', 'ongoing')
                ->whereDate('expected_return_date', '<', now())
                ->latest()
                ->take(5)
                ->get();

            $overdueCount = Loan::where('status', 'ongoing')
                ->whereDate('expected_return_date', '<', now())
                ->count();

            // 2. Low Stock (Quantity <= 5)
            $lowStockTools = Tool::with('location')
                ->where('quantity', '<=', 5)
                ->where('quantity', '>', 0)
                ->orderBy('quantity', 'asc')
                ->take(5)
                ->get();

            $lowStockCount = Tool::where('quantity', '<=', 5)->count();

            return [
                'notifOverdueLoans' => $overdueLoans,
                'notifOverdueCount' => $overdueCount,
                'notifLowStockTools' => $lowStockTools,
                'notifLowStockCount' => $lowStockCount,
                'totalNotifications' => $overdueCount + $lowStockCount
            ];
        });

        $view->with($data);
    }
}
