<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::where('email', 'test@example.com')->first();
if (!$user) {
    echo "User not found.\n";
    exit;
}

echo "Pass Hash: " . substr($user->password, 0, 10) . "...\n";
$check = Hash::check('password', $user->password);
echo "Check 'password': " . ($check ? "VALID" : "INVALID") . "\n";

$info = password_get_info($user->password);
echo "Algo: " . $info['algoName'] . "\n";
