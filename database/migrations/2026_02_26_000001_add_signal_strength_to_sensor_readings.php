<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add signal_strength column if it doesn't already exist
        if (!Schema::hasColumn('sensor_readings', 'signal_strength')) {
            Schema::table('sensor_readings', function (Blueprint $table) {
                $table->float('signal_strength')->nullable()->after('gas_level')
                      ->comment('WiFi RSSI in dBm from ESP32');
            });
        }
    }

    public function down(): void
    {
        Schema::table('sensor_readings', function (Blueprint $table) {
            $table->dropColumn('signal_strength');
        });
    }
};
