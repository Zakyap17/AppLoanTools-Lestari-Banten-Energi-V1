<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestEmailTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:send-emails {email : The recipient email address} {--type=all : The type of email (created, reminder, overdue, calibration, all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send dummy emails for verification. Usage: --type=created|reminder|overdue|calibration|all';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $type = $this->option('type');

        $this->info("Target: $email");
        $this->info("Mode: " . ucfirst($type));

        if ($type === 'all' || $type === 'created') {
            $this->info("1. Sending Loan Confirmation...");
            $loanDetails = [
                'employee_name' => 'John Doe (Test)',
                'loan_code' => 'LOAN-TEST-001',
                'loan_date' => date('Y-m-d'),
                'expected_return_date' => date('Y-m-d', strtotime('+7 days')),
                'notes' => 'Testing email design',
                'items' => [
                    [
                        'tool_name' => 'Bosch Impact Drill',
                        'tool_code' => 'TOOL-001',
                        'quantity' => 1
                    ],
                    [
                        'tool_name' => 'Fluke Multimeter',
                        'tool_code' => 'TOOL-002',
                        'quantity' => 1
                    ]
                ]
            ];
            \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\LoanCreated($loanDetails));
            $this->info("   -> Sent.");
            if ($type === 'all')
                sleep(2);
        }

        if ($type === 'all' || $type === 'reminder') {
            $this->info("2. Sending Loan Reminder (Late)...");

            // Mock Loan Object
            $loanMock = new \stdClass();
            $loanMock->loan_code = 'LOAN-TEST-002';
            $loanMock->loan_quantity = 2;
            $loanMock->loan_date = '2023-01-01';
            $loanMock->expected_return_date = '2023-01-08';

            $loanMock->borrower = new \stdClass();
            $loanMock->borrower->name = 'Jane Doe (Test)';

            $loanMock->tool = new \stdClass();
            $loanMock->tool->name = 'Safety Harness Kit';
            $loanMock->tool->code = 'SAFETY-005';

            \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\LoanReminder(collect([$loanMock]), 3));
            $this->info("   -> Sent.");
            if ($type === 'all')
                sleep(2);
        }

        if ($type === 'all' || $type === 'overdue') {
            $this->info("3. Sending Overdue Notification...");

            // Re-create mocks if needed
            if (!isset($loanMock)) {
                $loanMock = new \stdClass();
                $loanMock->loan_code = 'LOAN-TEST-XXX';
                $loanMock->loan_quantity = 1;
                $loanMock->loan_date = '2023-01-01';
                $loanMock->expected_return_date = '2023-01-08';
                $loanMock->borrower = new \stdClass();
                $loanMock->borrower->name = 'Jane Doe (Test)';
                $loanMock->tool = new \stdClass();
                $loanMock->tool->name = 'Test Tool';
                $loanMock->tool->code = 'TEST-001';
            }

            // Mock Collection of Loans
            $overdueLoan1 = clone $loanMock;
            $overdueLoan1->item = new \stdClass();
            $overdueLoan1->item->serial_number = 'SN-12345';
            $overdueLoan1->tool_item_id = 55;

            $overdueLoan2 = clone $loanMock;
            $overdueLoan2->loan_code = 'LOAN-TEST-003';
            $overdueLoan2->tool->name = 'Welding Machine';
            $overdueLoan2->item = null;
            $overdueLoan2->tool_item_id = null;
            $overdueLoan2->expected_return_date = '2023-05-20'; // Very late

            $loans = collect([$overdueLoan1, $overdueLoan2]);

            \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\OverdueLoanNotification('Jane Doe (Test)', $loans));
            $this->info("   -> Sent.");
            if ($type === 'all')
                sleep(2);
        }

        if ($type === 'all' || $type === 'calibration') {
            $this->info("4. Sending Calibration Reminder...");

            $toolMock = new \stdClass();
            $toolMock->name = 'Digital Micrometer';
            $toolMock->code = 'MEASURE-001';
            $toolMock->serial_number = 'SN-999-CAL';
            $toolMock->calibration_due_date = date('Y-m-d', strtotime('+5 days'));

            $tools = collect([$toolMock]);

            \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\CalibrationReminder($tools));
            $this->info("   -> Sent.");
        }

        $this->info('Done!');
    }
}
