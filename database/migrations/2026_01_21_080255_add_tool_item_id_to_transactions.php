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
        Schema::table('loans', function (Blueprint $table) {
            $table->foreignId('tool_item_id')->nullable()->after('tool_id')->constrained('tool_items')->nullOnDelete();
        });

        Schema::table('damage_losts', function (Blueprint $table) {
            $table->foreignId('tool_item_id')->nullable()->after('tool_id')->constrained('tool_items')->nullOnDelete();
        });

        // Assuming mutation_logs tracks individual movements
        Schema::table('mutation_logs', function (Blueprint $table) {
            $table->foreignId('tool_item_id')->nullable()->after('tool_id')->constrained('tool_items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['tool_item_id']);
            $table->dropColumn('tool_item_id');
        });

        Schema::table('damage_losts', function (Blueprint $table) {
            $table->dropForeign(['tool_item_id']);
            $table->dropColumn('tool_item_id');
        });

        Schema::table('mutation_logs', function (Blueprint $table) {
            $table->dropForeign(['tool_item_id']);
            $table->dropColumn('tool_item_id');
        });
    }
};
