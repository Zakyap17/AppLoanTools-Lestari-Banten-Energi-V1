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
            $table->foreignId('sub_group_id')->nullable()->after('type_id')->constrained('tools_mat_sub_groups')->nullOnDelete();
            $table->string('bin_code')->nullable()->after('location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->dropForeign(['sub_group_id']);
            $table->dropColumn(['sub_group_id', 'bin_code']);
        });
    }
};
