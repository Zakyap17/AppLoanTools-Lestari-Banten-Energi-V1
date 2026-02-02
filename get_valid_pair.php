<?php
use App\Models\Location;
use App\Models\Rack;
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$loc = Location::where('name', 'TOOLS ROOM B')->first();
if ($loc) {
    $rack = Rack::where('location_id', $loc->id)->first();
    echo "LOC: {$loc->name}\n";
    echo "RACK: " . ($rack ? $rack->code : "NONE") . "\n";
} else {
    echo "TOOLS ROOM B not found\n";
}
