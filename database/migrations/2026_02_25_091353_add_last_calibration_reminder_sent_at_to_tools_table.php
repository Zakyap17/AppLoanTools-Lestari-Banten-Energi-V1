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
            if (!Schema::hasColumn('tools', 'last_calibration_reminder_sent_at')) {
                $table->timestamp('last_calibration_reminder_sent_at')->nullable()->after('next_calibration_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            if (Schema::hasColumn('tools', 'last_calibration_reminder_sent_at')) {
                $table->dropColumn('last_calibration_reminder_sent_at');
            }
        });
    }
};
