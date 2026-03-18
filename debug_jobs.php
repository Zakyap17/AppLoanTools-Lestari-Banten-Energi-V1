<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Employee;

echo "--- JOBS TABLE ---\n";
$jobs = DB::table('jobs')->select('id', 'queue', 'attempts', 'reserved_at')->get();
foreach ($jobs as $job) {
    echo "ID: {$job->id}, Queue: {$job->queue}, Attempts: {$job->attempts}, Reserved At: " . ($job->reserved_at ? date('Y-m-d H:i:s', $job->reserved_at) : 'NULL') . "\n";
}

echo "\n--- NOVIYANTO SEARCH ---\n";
$novis = Employee::where('name', 'ilike', '%NOVIYANTO%')->get();
foreach ($novis as $n) {
    echo "ID: {$n->id}, Name: {$n->name}, Email: [" . ($n->email ?: 'EMPTY') . "]\n";
}

echo "\n--- RECENT LOANS ---\n";
$recentLoans = \App\Models\Loan::with('borrower')->latest()->take(5)->get();
foreach ($recentLoans as $l) {
    echo "Doc: {$l->loan_code}, Borrower: " . ($l->borrower->name ?? 'N/A') . ", Email: " . ($l->borrower->email ?? 'N/A') . "\n";
}
