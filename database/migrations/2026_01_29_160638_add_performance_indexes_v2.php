<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes silently (ignore if exists) to ensure robust migration
        $this->addIndexSilently('tool_items', 'tool_id', 'idx_tool_items_tool_id');
        $this->addIndexSilently('tool_items', 'serial_number', 'idx_tool_items_serial_number');

        $this->addIndexSilently('tools', 'name', 'idx_tools_name_v2');
        $this->addIndexSilently('tools', 'code', 'idx_tools_code_v2');
        $this->addIndexSilently('tools', 'location_id', 'idx_tools_location_id');
        $this->addIndexSilently('tools', 'rack_id', 'idx_tools_rack_id');

        $this->addIndexSilently('locations', 'name', 'idx_locations_name_v2');
        $this->addIndexSilently('racks', 'code', 'idx_racks_code_v2');

        $this->addIndexSilently('employees', 'name', 'idx_employees_name_v2');
        $this->addIndexSilently('manufacturers', 'name', 'idx_manufacturers_name_v2');
        $this->addIndexSilently('loans', 'loan_code', 'idx_loans_code');
        $this->addIndexSilently('loans', 'tool_id', 'idx_loans_tool_id');
        $this->addIndexSilently('loans', 'employee_id', 'idx_loans_employee_id');
    }

    private function addIndexSilently($table, $column, $indexName)
    {
        try {
            DB::statement("CREATE INDEX $indexName ON $table ($column)");
        } catch (\Exception $e) {
            // Ignore Duplicate Index Error
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropIndexSilently('idx_tool_items_tool_id');
        $this->dropIndexSilently('idx_tool_items_serial_number');
        $this->dropIndexSilently('idx_tools_name_v2');
        $this->dropIndexSilently('idx_tools_code_v2');
        $this->dropIndexSilently('idx_tools_location_id');
        $this->dropIndexSilently('idx_tools_rack_id');
        $this->dropIndexSilently('idx_locations_name_v2');
        $this->dropIndexSilently('idx_racks_code_v2');
        $this->dropIndexSilently('idx_employees_name_v2');
        $this->dropIndexSilently('idx_manufacturers_name_v2');
        $this->dropIndexSilently('idx_loans_code');
        $this->dropIndexSilently('idx_loans_tool_id');
        $this->dropIndexSilently('idx_loans_employee_id');
    }

    private function dropIndexSilently($indexName)
    {
        try {
            DB::statement("DROP INDEX IF EXISTS $indexName");
        } catch (\Exception $e) {
            // Ignore
        }
    }
};
