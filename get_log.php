<?php
$log = file('storage/logs/laravel.log');
for ($i = count($log) - 1; $i >= 0; $i--) {
    if (strpos($log[$i], 'Attempt to read property "code" on null') !== false) {
        for ($j = max(0, $i - 5); $j < min(count($log), $i + 20); $j++) {
            echo $log[$j];
        }
        break;
    }
}
