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
        echo "User #" . ($index + 1) . ":\n";
        echo " - Name: " . ($user['name'] ?? 'N/A') . "\n";
        echo " - Username: " . ($user['username'] ?? 'N/A') . "\n";
        echo " - NIK (detected): " . ($user['nik'] ?? 'EMPTY') . "\n";
        
        if (!empty($user['nik'])) {
            try {
                echo " - Testing DB Save... ";
                $emp = \App\Models\Employee::updateOrCreate(
                    ['nik' => $user['nik']],
                    [
                        'name' => $user['name'],
                        'department' => $user['department'] ?? '-',
                        'position' => $user['position'] ?? '-',
                        'email' => $user['email'] ?? null,
                    ]
                );
                echo "SUCCESS (ID: {$emp->id})\n";
            } catch (\Exception $e) {
                echo "FAILED: " . $e->getMessage() . "\n";
            }
        } else {
            echo " - Skipping DB test (NIK empty).\n";
        }

        echo " - Available Attributes: " . implode(', ', $user['raw_keys'] ?? []) . "\n";
        echo "---------------------------\n";
    }
} else {
    echo "No users found. Check LDAP_BASE_DN or filters.\n";
}

$count = \App\Models\Employee::count();
echo "\nTotal Employees in DB now: " . $count . "\n";

echo "--- LDAP DIAGNOSTIC END ---\n";
