<?php

use App\Models\Location;
use App\Models\Rack;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Locations...\n";
$locations = Location::all();
foreach ($locations as $loc) {
    echo "ID: {$loc->id} | Code: '{$loc->code}' | Name: '{$loc->name}'\n";
}

echo "\nChecking Racks...\n";
$racks = Rack::all();
foreach ($racks as $rack) {
    echo "ID: {$rack->id} | LocID: {$rack->location_id} | Code: '{$rack->code}' | Desc: '{$rack->description}'\n";
}
