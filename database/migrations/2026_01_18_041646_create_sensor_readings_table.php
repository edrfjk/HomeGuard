<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            
            // Sensor data (from ESP32)
            $table->float('temperature'); // °C from DHT22
            $table->float('humidity'); // % from DHT22
            $table->float('gas_level'); // PPM from MQ-2
            $table->string('gas_status')->default('safe'); // safe, warning, critical
            
            // Signal
            $table->string('signal_strength')->nullable(); // WiFi RSSI
            
            $table->timestamps();
            
            $table->index(['device_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
    }
};