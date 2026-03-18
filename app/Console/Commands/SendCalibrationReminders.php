<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tool;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\CalibrationReminder;

class SendCalibrationReminders extends Command
{
    protected $signature = 'app:send-calibration-reminders';
    protected $description = 'Send reminders for tools needing calibration (H-30 on Mondays, and daily from H-14)';

    public function handle()
    {
        $today = Carbon::now()->startOfDay();

        // Get tools that have a next calibration date and have NOT received a reminder today
        $tools = Tool::whereNotNull('next_calibration_date')
            ->where(function ($query) use ($today) {
                $query->whereNull('last_calibration_reminder_sent_at')
                    ->orWhereDate('last_calibration_reminder_sent_at', '<', $today->toDateString());
            })
            ->get();

        $toolsToRemind = collect();

        foreach ($tools as $tool) {
            $calDate = Carbon::parse($tool->next_calibration_date)->startOfDay();
            $daysLeft = (int) $today->diffInDays($calDate, false);

            if ($daysLeft <= 14) {
                // If it's H-14 or less (including overdue), send daily
                $toolsToRemind->push($tool);
            } elseif ($daysLeft <= 30 && $daysLeft > 14) {
                // If it's between H-30 and H-15, only send on Mondays (1 = Monday)
                if ($today->dayOfWeek === Carbon::MONDAY) {
                    $toolsToRemind->push($tool);
                }
            }
        }

        if ($toolsToRemind->isEmpty()) {
            $this->info("No tools require calibration reminders today.");
            return;
        }

        $this->info("Found {$toolsToRemind->count()} tools needing calibration reminders.");

        $mgmtEmails = config('mail.management_emails');
        if ($mgmtEmails) {
            try {
                $emails = array_filter(array_map('trim', explode(',', $mgmtEmails)));
                if (!empty($emails)) {
                    $mail = Mail::to($emails[0]);
                    if (count($emails) > 1) {
                        $mail->bcc(array_slice($emails, 1));
                    }
                }

                $mail->send(new CalibrationReminder($toolsToRemind));

                // Log success
                \Illuminate\Support\Facades\Log::info("Calibration Reminder dikirim ke: " . implode(', ', $emails));

                // Update the sent timestamp so it isn't sent again today
                foreach ($toolsToRemind as $tool) {
                    $tool->update(['last_calibration_reminder_sent_at' => now()]);
                }

                $this->info('Calibration reminders sent successfully.');
            } catch (\Exception $e) {
                $this->error("Failed to send calibration reminders: " . $e->getMessage());
            }
        } else {
            $this->warn('MANAGEMENT_EMAILS is not set in .env. No emails sent.');
        }
    }
}
