<?php
use App\Models\Tool;
use App\Models\CalibrationIssuing;
use Carbon\Carbon;

echo "--- Tools Near Calibration (Dashboard Logic) ---\n";
$dueTools = Tool::whereNotNull('next_calibration_date')
    ->where('next_calibration_date', '<=', Carbon::now()->addDays(14))
    ->get();
echo "Count: " . $dueTools->count() . "\n";
foreach ($dueTools as $t) {
    echo "Tool {$t->code}: Next Cal = {$t->next_calibration_date}\n";
}

echo "\n--- Calibration Issuings (Transaction Logic) ---\n";
$issues = CalibrationIssuing::with('tool')->where('status', 'in_progress')->get();
foreach ($issues as $iss) {
    echo "Issue {$iss->issue_code}: Tool {$iss->tool->code}, Exp Completion = {$iss->expected_completion_date}, Tool Next Cal = {$iss->tool->next_calibration_date}\n";
    $diff = Carbon::now()->diffInDays(Carbon::parse($iss->expected_completion_date), false);
    echo "Countdown (Days Left): " . round($diff) . "\n";
}
