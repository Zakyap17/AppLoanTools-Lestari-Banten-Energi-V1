<?php

use App\Models\Location;
use App\Models\Rack;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$data = [
    'locations' => Location::select('id', 'name', 'code')->get()->toArray(),
    'racks' => Rack::with('location:id,name')->select('id', 'location_id', 'code', 'description')->get()->toArray()
];

echo json_encode($data, JSON_PRETTY_PRINT);
