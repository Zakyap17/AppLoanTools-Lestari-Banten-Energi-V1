<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Tool Material Groups
        Schema::create('tools_mat_groups', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 2. Tool Material Sub Groups
        Schema::create('tools_mat_sub_groups', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., 'G001-S01'
            $table->string('mat_group_code');
            $table->string('description')->nullable();
            $table->timestamps();

            // FK to groups usually via code or id. Using code as implied by model relation
            // $table->foreign('mat_group_code')->references('code')->on('tools_mat_groups'); 
        });

        // 3. Locations (Storage)
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            // Form Storage
            $table->string('code')->unique(); // Storage Code
            $table->string('name'); // Storage Name
            $table->string('storage_type')->nullable();
            $table->string('warehouse')->nullable();
            $table->integer('total_bin')->default(0);
            $table->timestamps();
        });

        // 3b. Racks
        Schema::create('racks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();
            $table->string('code'); // Rack Code
            $table->string('description')->nullable();
            $table->string('room')->nullable();
            $table->string('building')->nullable();
            $table->timestamps();
        });

        // 4. Tools Types
        Schema::create('tools_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 5. Manufacturers
        Schema::create('manufacturers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        // 6. Vendor Calibrations
        Schema::create('vendor_calibrations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        // 7. Employees
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique();
            $table->string('name');
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        // 8. Tools (The main asset table)
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('serial_number')->nullable();
            $table->string('specification')->nullable();

            // Foreign Keys / References
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('type_id')->nullable()->constrained('tools_types')->nullOnDelete();
            $table->foreignId('manufacturer_id')->nullable()->constrained('manufacturers')->nullOnDelete();

            // Should probably link to sub_group too
            // $table->foreignId('sub_group_id')->nullable();

            $table->date('purchase_date')->nullable();
            $table->date('calibration_date')->nullable();
            $table->date('next_calibration_date')->nullable();

            $table->string('status')->default('available'); // available, in_use, damage, lost, calibration
            $table->integer('quantity')->default(0); // For non-serialized items or tracking total availability
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Add Check Constraint to prevent negative stock (PostgreSQL specific, but standard SQL)
        // This ensures the database REJECTS any transaction that results in quantity < 0
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE tools ADD CONSTRAINT check_quantity_non_negative CHECK (quantity >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('tools');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('vendor_calibrations');
        Schema::dropIfExists('manufacturers');
        Schema::dropIfExists('tools_types');
        Schema::dropIfExists('racks');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('tools_mat_sub_groups');
        Schema::dropIfExists('tools_mat_groups');
    }
};
