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

        $this->info("Found {$loans->count()} items in active loans. Checking for reminders...");

        // Group by loan_code to send one email per document
        $groupedLoans = $loans->groupBy('loan_code');

        $bar = $this->output->createProgressBar($groupedLoans->count());
        $bar->start();

        foreach ($groupedLoans as $loanCode => $items) {
            $firstLoan = $items->first();
            $expectedReturn = \Carbon\Carbon::parse($firstLoan->expected_return_date)->startOfDay();
            $today = \Carbon\Carbon::now()->startOfDay();

            $diff = $today->diffInDays($expectedReturn, false);
            $daysLeft = (int) $diff;

            $isMonday = $today->isMonday();
            $shouldSendReminder = false;

            // Business logic:
            // 1. If > 7 days remaining, only send on Mondays.
            // 2. If <= 7 days remaining (but >= 0, meaning not overdue yet), send every day.
            if ($daysLeft > 7 && $isMonday) {
                $shouldSendReminder = true;
            } elseif ($daysLeft >= 0 && $daysLeft <= 7) {
                $shouldSendReminder = true;
            }

            // Send reminder if criteria met
            if ($shouldSendReminder) {
                if ($firstLoan->borrower && $firstLoan->borrower->email) {
                    try {
                        $mail = \Illuminate\Support\Facades\Mail::to($firstLoan->borrower->email);

                        // Add Management BCC
                        $mgmtEmails = env('MANAGEMENT_EMAILS');
                        if ($mgmtEmails) {
                            $emails = array_map('trim', explode(',', $mgmtEmails));
                            $mail->bcc($emails);
                        }

                        // Send email with ALL items in this document
                        $mail->send(new \App\Mail\LoanReminder($items, $daysLeft));

                        // Update last_reminder_sent_at for all items in the group
                        foreach ($items as $loan) {
                            $loan->update(['last_reminder_sent_at' => now()]);
                        }
                    } catch (\Exception $e) {
                        $this->error("Failed to send to {$firstLoan->borrower->email} for loan {$loanCode}: " . $e->getMessage());
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
