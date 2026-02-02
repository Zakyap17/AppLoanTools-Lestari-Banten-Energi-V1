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
        Schema::table('tools_mat_sub_groups', function (Blueprint $table) {
            $table->renameColumn('code', 'sub_code');
            $table->renameColumn('description', 'sub_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tools_mat_sub_groups', function (Blueprint $table) {
            $table->renameColumn('sub_code', 'code');
            $table->renameColumn('sub_description', 'description');
        });
    }
};
