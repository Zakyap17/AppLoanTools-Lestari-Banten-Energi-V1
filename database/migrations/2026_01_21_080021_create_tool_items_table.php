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
        Schema::create('tool_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tool_id')->constrained('tools')->cascadeOnDelete();
            $table->string('serial_number')->unique(); // The itemized code: MT00001-001
            $table->string('status')->default('available'); // available, in_use, damage, lost, calibration
            $table->string('condition')->nullable(); // good, minor_damage, etc.
            $table->date('purchase_date')->nullable();
            $table->timestamps();
        });

        // Auto-migrate existing Quantities to Items
        $tools = \Illuminate\Support\Facades\DB::table('tools')->get();
        foreach ($tools as $tool) {
            if ($tool->quantity > 0) {
                for ($i = 1; $i <= $tool->quantity; $i++) {
                    $seq = str_pad($i, 3, '0', STR_PAD_LEFT);
                    \Illuminate\Support\Facades\DB::table('tool_items')->insert([
                        'tool_id' => $tool->id,
                        'serial_number' => $tool->code . '-' . $seq,
                        'status' => 'available', // Defaulting to available, assuming no active loans migrated yet (simplification)
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tool_items');
    }
};
