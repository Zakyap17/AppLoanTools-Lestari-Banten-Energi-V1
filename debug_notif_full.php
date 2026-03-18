<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;

echo "--- Current Time: " . now() . " ---\n";

// 1. Check Recently Failed Jobs
$recentFailures = DB::table('failed_jobs')
    ->where('failed_at', '>', now()->subHours(24))
    ->count();

echo "Failures in last 24h: $recentFailures\n";

if ($recentFailures > 0) {
    $latest = DB::table('failed_jobs')->latest('failed_at')->first();
    echo "Latest failure at: {$latest->failed_at}\n";
    echo "Exception snippet: " . substr($latest->exception, 0, 200) . "\n";
}

// 2. Check Pending Jobs
$pending = DB::table('jobs')->count();
echo "Pending Jobs in 'jobs' table: $pending\n";

// 3. Test SMTP Connectivity (Directly)
echo "\n--- Testing SMTP Connectivity ---\n";
$host = env('MAIL_HOST');
$port = env('MAIL_PORT', 25);
echo "Connecting to $host:$port...\n";

$connection = @fsockopen($host, $port, $errno, $errstr, 5);
if ($connection) {
    echo "SUCCESS: Connected to SMTP server.\n";
    fclose($connection);
} else {
    echo "FAILURE: Could not connect to SMTP server. Error: $errstr ($errno)\n";
}

// 4. Try sending a real test mail (Sync)
echo "\n--- Sending Test Email (Sync) ---\n";
try {
    Mail::config(['mail.default' => 'smtp']);
    Mail::to('indra.setiawan@gentingenergy.com')->send(new TestMail());
    echo "SUCCESS: Test email sent successfully.\n";
} catch (\Exception $e) {
    echo "FAILURE: Could not send test email. Error: " . $e->getMessage() . "\n";
}
