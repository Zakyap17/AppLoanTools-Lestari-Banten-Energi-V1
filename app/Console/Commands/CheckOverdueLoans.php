<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckOverdueLoans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loan:send-overdue-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for overdue ongoing loans and send email notifications to borrowers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting overdue loan check...');

        // 1. Get Overdue Loans that haven't been notified TODAY
        $overdueLoans = \App\Models\Loan::with(['tool', 'item', 'borrower'])
            ->where('status', 'ongoing')
            ->whereDate('expected_return_date', '<', now()->toDateString())
            ->where(function ($query) {
                $query->whereNull('last_reminder_sent_at')
                    ->orWhereDate('last_reminder_sent_at', '<', now()->toDateString());
            })
            ->get();

        if ($overdueLoans->isEmpty()) {
            // Quietly exit or log mostly empty run
            return;
        }

        $this->info('Found ' . $overdueLoans->count() . ' overdue items.');

        // 2. Group by Borrower to send 1 email per person
        $loansByBorrower = $overdueLoans->groupBy('employee_id');

        foreach ($loansByBorrower as $employeeId => $loans) {
            $borrower = $loans->first()->borrower;

            if (!$borrower || !$borrower->email) {
                $this->warn("Skipping Borrower ID {$employeeId}: No Email Address found.");
                continue;
            }

            // Check if ALL loans in this group have already been sent a reminder TODAY
            $allSentToday = $loans->every(function ($loan) {
                return $loan->last_reminder_sent_at && \Carbon\Carbon::parse($loan->last_reminder_sent_at)->isToday();
            });

            if ($allSentToday) {
                $this->info("Skipping {$borrower->name}: Already sent reminder for these items today.");
                continue;
            }

            $this->info("Sending email to {$borrower->name} ({$borrower->email}) - " . $loans->count() . " items.");

            try {
                \Illuminate\Support\Facades\Mail::to($borrower->email)
                    ->send(new \App\Mail\OverdueLoanNotification($borrower->name, $loans));
                $this->info("Email sent successfully.");

                // Update last_reminder_sent_at for ALL loans in this group
                foreach ($loans as $loan) {
                    $loan->update(['last_reminder_sent_at' => now()]);
                }

            } catch (\Exception $e) {
                $this->error("Failed to send email to {$borrower->email}: " . $e->getMessage());
            }
        }

        $this->info('Overdue check completed.');
    }
}
