<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $jobsCount = DB::table('jobs')->count();
    $failedJobsCount = DB::table('failed_jobs')->count();
    
    echo "Queued Jobs: " . $jobsCount . "\n";
    echo "Failed Jobs: " . $failedJobsCount . "\n";
    
    if ($failedJobsCount > 0) {
        $failedJobs = DB::table('failed_jobs')->latest('failed_at')->take(5)->get();
        foreach ($failedJobs as $job) {
            echo "--- Failed Job ID: {$job->id} ---\n";
            echo "Exception: " . substr($job->exception, 0, 500) . "...\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
