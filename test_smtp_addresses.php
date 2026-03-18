<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use App\Models\Employee;

echo "--- CHECKING IDHAM MAULANA ---\n";
$idham = Employee::where('name', 'ilike', '%IDHAM%')->first();
if ($idham) {
    echo "Name: {$idham->name}\n";
    echo "Email: [{$idham->email}]\n";
} else {
    echo "Idham not found!\n";
}

echo "\n--- TESTING BCC ADDRESSES ---\n";
$mgmtEmails = env('MANAGEMENT_EMAILS');
$emails = array_map('trim', explode(',', $mgmtEmails));

foreach ($emails as $email) {
    try {
        echo "Testing To: $email ... ";
        Mail::raw('Test SMTP validity', function ($msg) use ($email) {
            $msg->to($email)->subject('SMTP VALIDITY TEST');
        });
        echo "OK\n";
    } catch (\Exception $e) {
        echo "FAILED: " . $e->getMessage() . "\n";
    }
}
