<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$job = \DB::table('failed_jobs')->orderBy('id', 'desc')->first();
if ($job) {
    echo "ID: " . $job->id . "\n";
    echo "Failed At: " . $job->failed_at . "\n";
    echo "Exception: \n" . substr($job->exception, 0, 1000) . "\n";
} else {
    echo "No failed jobs found.\n";
}
