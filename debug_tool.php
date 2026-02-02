<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tool = \App\Models\Tool::where('code', 'MT00001')->first();
if ($tool) {
    echo "Code: " . $tool->code . "\n";
    echo "Quantity: " . $tool->quantity . "\n";
    echo "Status: " . $tool->status . "\n";
} else {
    echo "Tool not found.\n";
}
