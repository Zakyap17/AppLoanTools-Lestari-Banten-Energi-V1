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
            $table->foreignId('keeper_id')->nullable()->after('employee_id')->constrained('employees')->nullOnDelete();
            $table->integer('loan_quantity')->default(1)->after('expected_return_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['keeper_id']);
            $table->dropColumn(['keeper_id', 'loan_quantity']);
        });
    }
};
