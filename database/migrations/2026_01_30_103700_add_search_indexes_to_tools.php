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
        try {
            Schema::table('tools', function (Blueprint $table) {
                // Add indexes for faster search on name and code
                $table->index('name');
                $table->index('code');
            });
        } catch (\Exception $e) {
            // Index likely exists
        }

        try {
            Schema::table('tool_items', function (Blueprint $table) {
                // Add index for serial number search
                $table->index('serial_number');
            });
        } catch (\Exception $e) {
            // Index likely exists
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['code']);
        });

        Schema::table('tool_items', function (Blueprint $table) {
            $table->dropIndex(['serial_number']);
        });
    }
};
