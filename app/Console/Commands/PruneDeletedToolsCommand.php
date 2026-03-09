<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PruneDeletedToolsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:prune';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete tools and tool items that were soft-deleted more than 7 days ago';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $threshold = \Carbon\Carbon::now()->subDays(7);

        // Delete Tool Items first (if they aren't cascading at DB level, its safer to do manually)
        $itemsDeleted = \App\Models\ToolItem::onlyTrashed()
            ->where('deleted_at', '<=', $threshold)
            ->forceDelete();

        // Delete Parent Tools
        $toolsDeleted = \App\Models\Tool::onlyTrashed()
            ->where('deleted_at', '<=', $threshold)
            ->forceDelete();

        $this->info("Pruned {$toolsDeleted} tools and {$itemsDeleted} tool items.");
    }
}
