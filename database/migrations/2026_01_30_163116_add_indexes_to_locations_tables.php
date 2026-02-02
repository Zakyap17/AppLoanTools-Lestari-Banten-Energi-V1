<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Add indexes for bin_locations and store_locations
        try {
            DB::statement('CREATE INDEX idx_bin_locations_store_loc ON bin_locations(store_location_id)');
        } catch (\Exception $e) {
        }

        try {
            DB::statement('CREATE INDEX idx_store_locations_name ON store_locations(name)');
        } catch (\Exception $e) {
        }
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS idx_bin_locations_store_loc');
        DB::statement('DROP INDEX IF EXISTS idx_store_locations_name');
    }
};
