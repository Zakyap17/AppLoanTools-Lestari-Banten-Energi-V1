<?php

use App\Models\Loan;
use Carbon\Carbon;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Current Time: " . now() . "\n";
echo "Current Time (Date): " . now()->toDateString() . "\n\n";

// Logic from CheckOverdueLoans
$overdueLoans1 = Loan::with(['tool', 'item', 'borrower'])
    ->where('status', 'ongoing')
    ->whereDate('expected_return_date', '<', now()->toDateString())
    ->where(function ($query) {
        $query->whereNull('last_reminder_sent_at')
            ->orWhereDate('last_reminder_sent_at', '<', now()->toDateString());
    })
    ->get();

echo "--- Logic 1 (CheckOverdueLoans) ---\n";
echo "Found: " . $overdueLoans1->count() . " loans pending notification.\n";
foreach ($overdueLoans1 as $loan) {
    echo "- Loan ID: {$loan->id}, Borrower: {$loan->borrower->name}, Due: {$loan->expected_return_date}, LastSent: " . ($loan->last_reminder_sent_at ?? 'NEVER') . "\n";
}

echo "\n";

// Logic from SendLoanReminders (Pending view of file, but assuming similar)
// Just dumping raw overdue loans
$allOverdue = Loan::where('status', 'ongoing')
    ->whereDate('expected_return_date', '<', now()->toDateString())
    ->get();

echo "--- All Overdue Loans (Raw) ---\n";
echo "Total Overdue: " . $allOverdue->count() . "\n";
foreach ($allOverdue as $loan) {
    echo "- Loan ID: {$loan->id}, Borrower: " . ($loan->borrower->name ?? 'Unknown') . ", Due: {$loan->expected_return_date}, LastSent: " . ($loan->last_reminder_sent_at ?? 'NEVER') . "\n";
}
