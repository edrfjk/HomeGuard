<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Device;
use App\Models\SensorReading;
use App\Models\Alert;
use App\Models\SafetyThreshold;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create test user
                $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ]
        );


        // Create test device
        $device = Device::create([
            'user_id' => $user->id,
            'name' => 'Living Room Camera',
            'device_id' => 'ESP32-' . substr(md5(time()), 0, 12),
            'location' => 'Living Room',
            'description' => 'Main living room camera with environmental sensors',
            'status' => 'online',
            'firmware_version' => '1.0.0',
        ]);

        // Create safety thresholds
        $threshold = SafetyThreshold::create([
            'device_id' => $device->id,
            'temp_warning' => 32,
            'temp_critical' => 38,
            'humidity_warning' => 65,
            'humidity_critical' => 85,
            'gas_warning' => 400,
            'gas_critical' => 800,
        ]);

        // Create fake sensor readings (24 hours of data)
        // Mix of NORMAL and ALERT-TRIGGERING readings
        for ($i = 0; $i < 24; $i++) {
            // Some readings trigger alerts
            if ($i === 5) {
                // HIGH TEMPERATURE - TRIGGERS WARNING
                $temp = 33;
                $humidity = 50;
                $gas = 200;
            } elseif ($i === 8) {
                // CRITICAL TEMPERATURE
                $temp = 39;
                $humidity = 50;
                $gas = 200;
            } elseif ($i === 12) {
                // HIGH HUMIDITY - TRIGGERS WARNING
                $temp = 25;
                $humidity = 70;
                $gas = 200;
            } elseif ($i === 15) {
                // CRITICAL HUMIDITY
                $temp = 25;
                $humidity = 88;
                $gas = 200;
            } elseif ($i === 18) {
                // HIGH GAS - TRIGGERS WARNING
                $temp = 25;
                $humidity = 50;
                $gas = 450;
            } elseif ($i === 20) {
                // CRITICAL GAS LEVEL
                $temp = 25;
                $humidity = 50;
                $gas = 850;
            } else {
                // Normal readings
                $temp = 24 + rand(-2, 5);
                $humidity = 50 + rand(-10, 10);
                $gas = 200 + rand(-50, 100);
            }

            $reading = SensorReading::create([
                'user_id' => $user->id,
                'device_id' => $device->id,
                'temperature' => $temp,
                'humidity' => $humidity,
                'gas_level' => $gas,
                'gas_status' => $gas >= $threshold->gas_critical ? 'critical' : ($gas >= $threshold->gas_warning ? 'warning' : 'safe'),
                'created_at' => now()->subHours($i),
                'updated_at' => now()->subHours($i),
            ]);

            // AUTO-CREATE ALERTS for out-of-threshold values
            $alerts = [];

            if ($temp >= $threshold->temp_critical) {
                $alerts[] = [
                    'type' => 'temperature_critical',
                    'severity' => 'critical',
                    'message' => "CRITICAL: Temperature is {$temp}°C (Threshold: {$threshold->temp_critical}°C)",
                ];
            } elseif ($temp >= $threshold->temp_warning) {
                $alerts[] = [
                    'type' => 'temperature_warning',
                    'severity' => 'warning',
                    'message' => "WARNING: Temperature is {$temp}°C (Threshold: {$threshold->temp_warning}°C)",
                ];
            }

            if ($humidity >= $threshold->humidity_critical) {
                $alerts[] = [
                    'type' => 'humidity_critical',
                    'severity' => 'critical',
                    'message' => "CRITICAL: Humidity is {$humidity}% (Threshold: {$threshold->humidity_critical}%)",
                ];
            } elseif ($humidity >= $threshold->humidity_warning) {
                $alerts[] = [
                    'type' => 'humidity_warning',
                    'severity' => 'warning',
                    'message' => "WARNING: Humidity is {$humidity}% (Threshold: {$threshold->humidity_warning}%)",
                ];
            }

            if ($gas >= $threshold->gas_critical) {
                $alerts[] = [
                    'type' => 'gas_critical',
                    'severity' => 'critical',
                    'message' => "CRITICAL: Gas detected at {$gas} PPM (Threshold: {$threshold->gas_critical} PPM)",
                ];
            } elseif ($gas >= $threshold->gas_warning) {
                $alerts[] = [
                    'type' => 'gas_warning',
                    'severity' => 'warning',
                    'message' => "WARNING: Gas detected at {$gas} PPM (Threshold: {$threshold->gas_warning} PPM)",
                ];
            }

            // Create all alerts
            foreach ($alerts as $alertData) {
                Alert::create([
                    'user_id' => $user->id,
                    'device_id' => $device->id,
                    'sensor_reading_id' => $reading->id,
                    'type' => $alertData['type'],
                    'severity' => $alertData['severity'],
                    'message' => $alertData['message'],
                    'reading_value' => $temp,
                    'status' => 'active',
                    'created_at' => now()->subHours($i),
                    'updated_at' => now()->subHours($i),
                ]);
            }
        }

        $this->command->info('✅ Test data created with ' . Alert::count() . ' sample alerts!');
    }
}