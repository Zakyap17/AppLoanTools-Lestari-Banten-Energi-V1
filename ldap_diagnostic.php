<?php

use App\Services\LdapService;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- LDAP DIAGNOSTIC START ---\n";

$ldap = new LdapService();
$users = $ldap->getAllUsers();

echo "Total Users Found: " . count($users) . "\n\n";

if (count($users) > 0) {
    foreach ($users as $index => $user) {
        if ($index > 2) break; // Only show first 3
        echo "User #" . ($index + 1) . ":\n";
        echo " - Name: " . ($user['name'] ?? 'N/A') . "\n";
        echo " - Username: " . ($user['username'] ?? 'N/A') . "\n";
        echo " - NIK (detected): " . ($user['nik'] ?? 'EMPTY') . "\n";
        echo " - Available Attributes: " . implode(', ', $user['raw_keys'] ?? []) . "\n";
        echo "---------------------------\n";
    }
} else {
    echo "No users found. Check LDAP_BASE_DN or filters.\n";
}

echo "--- LDAP DIAGNOSTIC END ---\n";
