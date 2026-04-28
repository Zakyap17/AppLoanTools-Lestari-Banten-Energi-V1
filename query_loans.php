<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$loans = \App\Models\Loan::where('status', 'ongoing')->get(['id', 'expected_return_date', 'last_reminder_sent_at']);
foreach($loans as $l) {
    echo "Loan {$l->id} - Expected: {$l->expected_return_date->format('Y-m-d')} - Last Sent: " . ($l->last_reminder_sent_at ? $l->last_reminder_sent_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
}
