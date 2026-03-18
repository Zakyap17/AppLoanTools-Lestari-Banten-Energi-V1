<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$job = DB::table('failed_jobs')->latest('failed_at')->first();
if ($job) {
    echo "ID: " . $job->id . "\n";
    echo "Queue: " . $job->queue . "\n";
    echo "Failed At: " . $job->failed_at . "\n";
    echo "Exception: \n" . $job->exception . "\n";
} else {
    echo "No failed jobs found.\n";
}
