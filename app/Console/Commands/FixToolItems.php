<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tool;
use App\Models\ToolItem;

class FixToolItems extends Command
{
    protected $signature = 'tools:fix-items';
    protected $description = 'Generate missing serialized items for tools based on quantity';

    public function handle()
    {
        $tools = Tool::all();
        $this->info("Scanning " . $tools->count() . " tools...");

        foreach ($tools as $tool) {
            $currentCount = $tool->items()->count();

            if ($currentCount < $tool->quantity) {
                $diff = $tool->quantity - $currentCount;
                $this->info("Fixing {$tool->code}: Found $currentCount, Expected {$tool->quantity}. Adding $diff items.");

                // Find last sequence
                $lastItem = $tool->items()->orderBy('id', 'desc')->first();
                $lastSeq = 0;
                if ($lastItem) {
                    $parts = explode('-', $lastItem->serial_number);
                    if (count($parts) > 1) {
                        $lastSeq = intval(end($parts));
                    }
                }

                for ($i = 1; $i <= $diff; $i++) {
                    $currentSeq = $lastSeq + $i;
                    $seqStr = str_pad($currentSeq, 3, '0', STR_PAD_LEFT);

                    ToolItem::create([
                        'tool_id' => $tool->id,
                        'serial_number' => $tool->code . '-' . $seqStr,
                        'status' => 'available',
                        'condition' => 'Good',
                        'purchase_date' => now()
                    ]);
                }
            }
        }
        $this->info("Done.");
    }
}
