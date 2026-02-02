<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ToolsRoomSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure Room A exists
        DB::table('locations')->updateOrInsert(
            ['code' => 'ROOM-A'],
            [
                'name' => 'TOOLS ROOM A',
                'storage_type' => 'Room',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Ensure Room B exists
        DB::table('locations')->updateOrInsert(
            ['code' => 'ROOM-B'],
            [
                'name' => 'TOOLS ROOM B',
                'storage_type' => 'Room',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
