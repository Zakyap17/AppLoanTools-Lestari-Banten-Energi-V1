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
        Schema::create('mutation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tool_id')->constrained('tools')->cascadeOnDelete();

            // From Location
            $table->foreignId('from_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('from_rack_id')->nullable()->constrained('racks')->nullOnDelete();

            // To Location
            $table->foreignId('to_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('to_rack_id')->nullable()->constrained('racks')->nullOnDelete();

            $table->text('reason')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutation_logs');
    }
};
