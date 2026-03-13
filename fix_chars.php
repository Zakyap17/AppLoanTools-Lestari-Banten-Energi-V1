<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tool;

$tools = Tool::where('name', 'like', "%\xEF\xBF\xBD%")->get();
echo "Found " . $tools->count() . " corrupted tools.\n";
foreach($tools as $tool) {
    echo "Old: " . $tool->name . "\n";
    
    // Replace the corrupted char \xEF\xBF\xBD with rules
    $newName = $tool->name;
    // For 133A.36-LIGHT ALLOY 90 OFFSET AMERICAN
    if (strpos($newName, '90') !== false && strpos($newName, 'OFFSET') !== false) {
        $newName = str_replace("\xEF\xBF\xBD", "°", $newName);
    }
    // For SET OF 9 PIECES OF 1/2" DRIVE TORX BIT
    elseif (strpos($newName, 'TORX') !== false) {
        $newName = str_replace("\xEF\xBF\xBD", "®", $newName);
    } 
    else {
        // generic fallback, assume degree
        $newName = str_replace("\xEF\xBF\xBD", "°", $newName);
    }
    
    $tool->name = $newName;
    $tool->save();
    
    echo "New: " . $tool->name . "\n";
}
echo "Done.\n";
