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
        Schema::table('vendor_calibrations', function (Blueprint $table) {
            $table->string('service')->nullable()->after('email');
            $table->string('post_code')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_calibrations', function (Blueprint $table) {
            $table->dropColumn(['service', 'post_code']);
        });
    }
};
