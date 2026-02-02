<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Loans (Peminjaman)
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_code')->unique();
            $table->foreignId('tool_id')->constrained('tools')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete(); // Borrower

            $table->date('loan_date');
            $table->date('expected_return_date')->nullable();
            $table->date('actual_return_date')->nullable();

            $table->string('status')->default('ongoing'); // ongoing, returned, overdue
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Damage / Lost
        Schema::create('damage_losts', function (Blueprint $table) {
            $table->id();
            $table->string('report_code')->unique();
            $table->foreignId('tool_id')->constrained('tools')->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete(); // Who reported/caused it

            $table->date('report_date');
            $table->string('type'); // damage or lost
            $table->string('severity')->nullable(); // low, medium, high
            $table->text('description')->nullable();
            $table->string('status')->default('open'); // open, resolved
            $table->timestamps();
        });

        // 3. Receives (Penerimaan Barang Baru / Kembali dari Service?)
        // Assuming this is for new items usually, or receiving generic items
        Schema::create('receives', function (Blueprint $table) {
            $table->id();
            $table->string('receive_code')->unique();
            $table->date('receive_date');
            $table->foreignId('received_by')->nullable()->constrained('employees'); // Employee who received
            $table->string('source')->nullable(); // Vendor name or PO number
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 4. Calibration Issuing (Pengeluaran untuk Kalibrasi)
        Schema::create('calibration_issuings', function (Blueprint $table) {
            $table->id();
            $table->string('issue_code')->unique();
            $table->foreignId('tool_id')->constrained('tools')->cascadeOnDelete();
            $table->foreignId('vendor_calibration_id')->constrained('vendor_calibrations');

            $table->date('issue_date');
            $table->date('expected_completion_date')->nullable();
            $table->date('completion_date')->nullable();

            $table->string('status')->default('in_progress'); // in_progress, completed
            $table->decimal('cost', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 5. Mutations (Perpindahan Lokasi)
        Schema::create('mutations', function (Blueprint $table) {
            $table->id();
            $table->string('mutation_code')->unique();
            $table->foreignId('tool_id')->constrained('tools')->cascadeOnDelete();

            $table->foreignId('origin_location_id')->nullable()->constrained('locations');
            $table->foreignId('destination_location_id')->constrained('locations');

            $table->date('mutation_date');
            $table->foreignId('authorized_by')->nullable()->constrained('employees');

            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutations');
        Schema::dropIfExists('calibration_issuings');
        Schema::dropIfExists('receives');
        Schema::dropIfExists('damage_losts');
        Schema::dropIfExists('loans');
    }
};
