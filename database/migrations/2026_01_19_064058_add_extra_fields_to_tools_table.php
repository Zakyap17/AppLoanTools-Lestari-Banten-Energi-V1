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
            $table->text('long_description')->nullable()->after('name');
            $table->string('type_model')->nullable()->after('type_id');
            $table->string('part_no')->nullable()->after('specification');
            $table->string('size')->nullable()->after('part_no');
            $table->string('uom')->nullable()->after('size');
            $table->decimal('price', 15, 2)->nullable()->after('uom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->dropColumn(['long_description', 'type_model', 'part_no', 'size', 'uom', 'price']);
        });
    }
};
