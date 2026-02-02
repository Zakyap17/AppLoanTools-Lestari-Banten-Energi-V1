<?php
use App\Models\ToolType;
use App\Models\Location;
use App\Models\Rack;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function dumpHex($str)
{
    if ($str === null)
        return "NULL";
    $hex = bin2hex($str);
    $formatted = chunk_split($hex, 2, ' ');
    return "String: '$str' | Length: " . strlen($str) . " | Hex: $formatted";
}

echo "--- CHECKING TOOL TYPES ---\n";
// Focus on "Hand Tools" analysis
$types = ToolType::where('name', 'LIKE', '%Hand%')->get();
if ($types->count() == 0)
    echo "No types found matching '%Hand%'\n";
foreach ($types as $t) {
    echo "ID: {$t->id} | " . dumpHex($t->name) . "\n";
}

echo "\n--- CHECKING LOCATIONS ---\n";
// Focus on "Room A" analysis (or similar)
$locs = Location::all();
foreach ($locs as $l) {
    echo "ID: {$l->id} | " . dumpHex($l->name) . "\n";
}
