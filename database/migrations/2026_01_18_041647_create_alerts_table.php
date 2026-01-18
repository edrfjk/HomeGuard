<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            $table->foreignId('sensor_reading_id')->nullable()->constrained()->onDelete('cascade');
            
            // Alert details
            $table->string('type'); // temperature_high, gas_detected, humidity_high
            $table->string('severity'); // critical, warning, info
            $table->text('message');
            $table->float('reading_value')->nullable();
            $table->float('threshold_value')->nullable();
            
            // Status
            $table->string('status')->default('active'); // active, acknowledged, resolved
            $table->datetime('acknowledged_at')->nullable();
            $table->datetime('resolved_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['user_id', 'status', 'created_at']);
            $table->index(['device_id', 'severity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};