<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use Raw SQL for PostgreSQL robustness (Idempotent)
        // Note: Standard B-Tree index helps with exact matches and sorting. 
        // For partial match %like%, it needs pg_trgm but we'll stick to standard first to avoid permission issues.

        try {
            \Illuminate\Support\Facades\DB::statement("CREATE INDEX idx_tools_name ON tools (name)");
        } catch (\Exception $e) {
        }
        try {
            \Illuminate\Support\Facades\DB::statement("CREATE INDEX idx_tools_code ON tools (code)");
        } catch (\Exception $e) {
        }
        try {
            \Illuminate\Support\Facades\DB::statement("CREATE INDEX idx_locations_name ON locations (name)");
        } catch (\Exception $e) {
        }
        try {
            \Illuminate\Support\Facades\DB::statement("CREATE INDEX idx_racks_code ON racks (code)");
        } catch (\Exception $e) {
        }
        try {
            \Illuminate\Support\Facades\DB::statement("CREATE INDEX idx_employees_name ON employees (name)");
        } catch (\Exception $e) {
        }
        try {
            \Illuminate\Support\Facades\DB::statement("CREATE INDEX idx_manufacturers_name ON manufacturers (name)");
        } catch (\Exception $e) {
        }
        try {
            \Illuminate\Support\Facades\DB::statement("CREATE INDEX idx_vendor_calibrations_name ON vendor_calibrations (name)");
        } catch (\Exception $e) {
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("DROP INDEX IF EXISTS idx_tools_name");
        \Illuminate\Support\Facades\DB::statement("DROP INDEX IF EXISTS idx_tools_code");
        \Illuminate\Support\Facades\DB::statement("DROP INDEX IF EXISTS idx_locations_name");
        \Illuminate\Support\Facades\DB::statement("DROP INDEX IF EXISTS idx_racks_code");
        \Illuminate\Support\Facades\DB::statement("DROP INDEX IF EXISTS idx_employees_name");
        \Illuminate\Support\Facades\DB::statement("DROP INDEX IF EXISTS idx_manufacturers_name");
        \Illuminate\Support\Facades\DB::statement("DROP INDEX IF EXISTS idx_vendor_calibrations_name");
    }
};
