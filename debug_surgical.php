<?php
use App\Models\ToolType;
use App\Models\Location;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function analyze($label, $value)
{
    echo "[$label] Found: '$value' | Len: " . strlen($value) . " | Hex: " . bin2hex($value) . "\n";
}

echo "--- DIAGNOSTIC START ---\n";

// 1. Exact match check
$t = ToolType::where('name', 'HAND TOOLS')->first();
if ($t)
    analyze("Exact 'HAND TOOLS'", $t->name);
else
    echo "Exact 'HAND TOOLS' not found.\n";

// 2. Case insensitive check via PHP
$allTypes = ToolType::all();
foreach ($allTypes as $t) {
    if (stripos($t->name, 'hand tools') !== false) {
        analyze("PHP stripos 'hand tools'", $t->name);
    }
}

// 3. Exact match check for Room A
$l = Location::where('name', 'Room A')->first();
if ($l)
    analyze("Exact 'Room A'", $l->name);
else
    echo "Exact 'Room A' not found.\n";

$l2 = Location::where('name', 'ROOM A')->first();
if ($l2)
    analyze("Exact 'ROOM A'", $l2->name);
else
    echo "Exact 'ROOM A' not found.\n";

// 4. Case insensitive check via PHP for Room A
$allLocs = Location::all();
foreach ($allLocs as $l) {
    if (stripos($l->name, 'Room A') !== false) {
        analyze("PHP stripos 'Room A'", $l->name);
    }
}

echo "--- DIAGNOSTIC END ---\n";
