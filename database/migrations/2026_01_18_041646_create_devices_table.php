<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "Living Room Camera"
            $table->string('device_id')->unique(); // ESP32 MAC address
            $table->string('location'); // e.g., "Living Room"
            $table->text('description')->nullable();
            $table->string('status')->default('offline'); // online, offline
            $table->string('ip_address')->nullable();
            $table->datetime('last_seen')->nullable();
            $table->string('firmware_version')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};