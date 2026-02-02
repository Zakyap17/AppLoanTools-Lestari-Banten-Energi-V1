<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendLoanReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-loan-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily reminders for loans due soon or overdue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $loans = \App\Models\Loan::with(['borrower', 'tool'])
            ->where('status', 'ongoing')
            ->whereNotNull('expected_return_date')
            ->where(function ($query) {
                $query->whereNull('last_reminder_sent_at')
                    ->orWhereDate('last_reminder_sent_at', '<', now()->toDateString());
            })
            ->get();

        $this->info("Found {$loans->count()} active loans. Checking for reminders...");

        $bar = $this->output->createProgressBar($loans->count());
        $bar->start();

        foreach ($loans as $loan) {
            $expectedReturn = \Carbon\Carbon::parse($loan->expected_return_date)->startOfDay();
            $today = \Carbon\Carbon::now()->startOfDay();

            // Check if we already sent a reminder today
            if ($loan->last_reminder_sent_at && \Carbon\Carbon::parse($loan->last_reminder_sent_at)->isSameDay($today)) {
                $this->info("Skipping loan {$loan->loan_code}: Reminder already sent today.");
                $bar->advance();
                continue;
            }

            $diff = $today->diffInDays($expectedReturn, false);
            $daysLeft = (int) $diff;

            // Send reminder if due in 5 days or less AND not overdue (0 to 5 days)
            // Overdue items (negative days) are handled by CheckOverdueLoans command
            if ($daysLeft >= 0 && $daysLeft <= 5) {
                if ($loan->borrower && $loan->borrower->email) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($loan->borrower->email)->send(new \App\Mail\LoanReminder($loan, $daysLeft));

                        // Update last_reminder_sent_at
                        $loan->update(['last_reminder_sent_at' => now()]);
                    } catch (\Exception $e) {
                        $this->error("Failed to send to {$loan->borrower->email}: " . $e->getMessage());
                    }
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Reminders sent successfully.');
    }
}
