<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM tools');
foreach ($columns as $column) {
    if ($column->Field === 'status') {
        echo "Field: " . $column->Field . "\n";
        echo "Type: " . $column->Type . "\n";
        echo "Default: " . $column->Default . "\n";
    }
}
