<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Device;
use App\Models\SafetyThreshold;
use App\Models\SensorReading;
use App\Models\CameraImage;
use App\Models\Alert;

class HomeGuardSeeder extends Seeder
{
    public function run(): void
    {
        /* =========================
         * USER
         * ========================= */
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ]
        );

        /* =========================
         * DEVICE
         * ========================= */
        $device = Device::firstOrCreate(
            [
                'user_id' => $user->id,
                'name' => 'Living Room Camera',
            ],
            [
                'device_id' => 'ESP32-' . substr(md5(time()), 0, 12),
                'location' => 'Living Room',
                'description' => 'Main living room camera with sensors',
                'status' => 'online',
                'firmware_version' => '1.0.0',
            ]
        );

        /* =========================
         * SAFETY THRESHOLDS
         * ========================= */
        $threshold = SafetyThreshold::firstOrCreate(
            ['device_id' => $device->id],
            [
                'temp_warning' => 32,
                'temp_critical' => 38,
                'humidity_warning' => 65,
                'humidity_critical' => 85,
                'gas_warning' => 400,
                'gas_critical' => 800,
            ]
        );

        /* =========================
         * SENSOR READINGS + ALERTS
         * ========================= */
        for ($i = 0; $i < 24; $i++) {

            $createdAt = now()->subHours($i);

            // Simulated values
            $temp = rand(24, 40);
            $humidity = rand(45, 90);
            $gas = rand(200, 900);

            $reading = SensorReading::create([
                'user_id' => $user->id,
                'device_id' => $device->id,
                'temperature' => $temp,
                'humidity' => $humidity,
                'gas_level' => $gas,
                'gas_status' => $gas >= $threshold->gas_critical
                    ? 'critical'
                    : ($gas >= $threshold->gas_warning ? 'warning' : 'safe'),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            if ($temp >= $threshold->temp_warning) {
                Alert::create([
                    'user_id' => $user->id,
                    'device_id' => $device->id,
                    'sensor_reading_id' => $reading->id,
                    'type' => 'temperature_alert',
                    'severity' => $temp >= $threshold->temp_critical ? 'critical' : 'warning',
                    'message' => "Temperature alert: {$temp}°C",
                    'reading_value' => $temp,
                    'status' => 'active',
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }

        /* =========================
         * CAMERA IMAGES + MOTION ALERTS
         * ========================= */
        $images = collect(Storage::disk('public')->files('camera_images'))
            ->filter(fn ($p) => preg_match('/\.(jpg|jpeg|png)$/i', $p))
            ->values();

        foreach ($images as $i => $path) {

            $timestamp = now()->subDays(rand(0, 30));

            $image = CameraImage::create([
                'user_id' => $user->id,
                'device_id' => $device->id,
                'filename' => basename($path),
                'image_path' => $path,
                'mime_type' => 'image/jpeg',
                'file_size' => Storage::disk('public')->size($path),
                'trigger_type' => 'motion',
                'caption' => 'Motion detected',
                'is_favorite' => $i % 3 === 0,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            Alert::create([
                'user_id' => $user->id,
                'device_id' => $device->id,
                'camera_image_id' => $image->id,
                'type' => 'motion_detected',
                'severity' => 'warning',
                'message' => "Motion detected in {$device->location}",
                'status' => 'active',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }

        $this->command->info('✅ HomeGuard full demo data seeded successfully!');
    }
}
