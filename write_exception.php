<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$job = DB::table('failed_jobs')->latest('failed_at')->first();
if ($job) {
    file_put_contents('failed_job_exception.txt', $job->exception);
    echo "Exception written to failed_job_exception.txt\n";
} else {
    echo "No failed jobs found.\n";
}
