<?php

use App\Models\ToolType;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$types = ToolType::select('id', 'code', 'name')->get();
echo json_encode($types->toArray(), JSON_PRETTY_PRINT);
