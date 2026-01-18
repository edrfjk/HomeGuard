<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('safety_thresholds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            
            // Temperature thresholds (°C)
            $table->float('temp_warning')->default(32);
            $table->float('temp_critical')->default(38);
            
            // Humidity thresholds (%)
            $table->float('humidity_warning')->default(65);
            $table->float('humidity_critical')->default(85);
            
            // Gas thresholds (PPM)
            $table->float('gas_warning')->default(400);
            $table->float('gas_critical')->default(800);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('safety_thresholds');
    }
};