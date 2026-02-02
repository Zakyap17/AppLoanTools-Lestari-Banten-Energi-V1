<?php

use App\Models\Location;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$locations = Location::select('id', 'code', 'name')->get();
echo json_encode($locations->toArray(), JSON_PRETTY_PRINT);
