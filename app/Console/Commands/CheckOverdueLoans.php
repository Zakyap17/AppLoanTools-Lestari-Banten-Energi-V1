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

        // 2. Group by Loan Code to send 1 email per document
        $loansByDocument = $overdueLoans->groupBy('loan_code');

        foreach ($loansByDocument as $loanCode => $loans) {
            $firstLoan = $loans->first();
            $borrower = $firstLoan->borrower;

            if (!$borrower || !$borrower->email) {
                $this->warn("Skipping Loan {$loanCode}: No Borrower Email Address found.");
                continue;
            }

            $this->info("Sending email to {$borrower->name} ({$borrower->email}) for Loan #{$loanCode} - " . $loans->count() . " items.");

            try {
                $mail = \Illuminate\Support\Facades\Mail::to($borrower->email);

                // Add Management BCC
                $mgmtEmails = config('mail.management_emails');
                if ($mgmtEmails) {
                    $emails = array_filter(array_map('trim', explode(',', $mgmtEmails)));
                    if (!empty($emails)) {
                        $mail->bcc($emails);
                    }
                }

                $mail->send(new \App\Mail\OverdueLoanNotification($borrower->name, $loans));
                $this->info("Email sent successfully.");

                // Update last_reminder_sent_at for ALL loans in this group
                foreach ($loans as $loan) {
                    $loan->update(['last_reminder_sent_at' => now()]);
                }

            } catch (\Exception $e) {
                $this->error("Failed to send email for Loan {$loanCode} to {$borrower->email}: " . $e->getMessage());
            }
        }

        $this->info('Overdue check completed.');
    }
}
