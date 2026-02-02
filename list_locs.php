<?php
use App\Models\Location;
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$locs = Location::pluck('name')->toArray();
echo "VALID LOCATIONS:\n";
foreach ($locs as $name) {
    echo "- $name\n";
}
