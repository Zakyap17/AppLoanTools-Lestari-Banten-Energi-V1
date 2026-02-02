<?php

use App\Models\Location;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Searching for locations containing 'Room'...\n";
$locations = Location::where('name', 'LIKE', '%Room%')->get();

foreach ($locations as $loc) {
    echo "ID: {$loc->id} | Code: '{$loc->code}' | Name: '{$loc->name}'\n";
}
