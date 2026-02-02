<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = \App\Models\Loan::where('status', 'ongoing')
    ->whereDate('expected_return_date', '<', now()->toDateString())
    ->count();

echo "Overdue Loan Count: " . $count . PHP_EOL;

// Check valid pairs for testing if count is 0
if ($count == 0) {
    $ongoing = \App\Models\Loan::where('status', 'ongoing')->first();
    if ($ongoing) {
        echo "Found ongoing loan (not overdue): ID " . $ongoing->id . " Expected: " . $ongoing->expected_return_date . PHP_EOL;
    } else {
        echo "No ongoing loans found." . PHP_EOL;
    }
}
