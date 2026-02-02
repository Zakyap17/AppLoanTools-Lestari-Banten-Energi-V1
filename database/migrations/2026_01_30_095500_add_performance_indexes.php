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
        Schema::table('tools', function (Blueprint $table) {
            // Index for faster search by name
            $table->index('name', 'tools_name_index');
        });

        Schema::table('tool_items', function (Blueprint $table) {
            // Index to speed up Joins with tools table
            $table->index('tool_id', 'tool_items_tool_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->dropIndex('tools_name_index');
        });

        Schema::table('tool_items', function (Blueprint $table) {
            $table->dropIndex('tool_items_tool_id_index');
        });
    }
};
