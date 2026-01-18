<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Device;
use App\Models\SensorReading;
use App\Models\SafetyThreshold;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create test user
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

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
        SafetyThreshold::create([
            'device_id' => $device->id,
            'temp_warning' => 32,
            'temp_critical' => 38,
            'humidity_warning' => 65,
            'humidity_critical' => 85,
            'gas_warning' => 400,
            'gas_critical' => 800,
        ]);

        // Create fake sensor readings (24 hours of data)
        for ($i = 0; $i < 24; $i++) {
            SensorReading::create([
                'user_id' => $user->id,
                'device_id' => $device->id,
                'temperature' => 24 + rand(-2, 8),
                'humidity' => 50 + rand(-10, 20),
                'gas_level' => 200 + rand(-50, 100),
                'gas_status' => 'safe',
                'created_at' => now()->subHours($i),
                'updated_at' => now()->subHours($i),
            ]);
        }
    }
}