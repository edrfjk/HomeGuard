<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Device;
use App\Models\Alert;
use App\Models\CameraImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class MotionDetectionSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            echo "❌ No user found.\n";
            return;
        }

        $device = $user->devices()->firstOrCreate(
            ['name' => 'Living Room Camera'],
            [
                'device_id' => 'ESP32-' . substr(md5(time()), 0, 12),
                'location' => 'Living Room',
                'description' => 'Main living room camera',
                'status' => 'online',
                'firmware_version' => '1.0.0',
            ]
        );

        echo "✓ Using User: {$user->name}\n";
        echo "✓ Using Device: {$device->name}\n\n";

        // Get all images from storage/app/public/camera_images
        $localImages = collect(
            Storage::disk('public')->files('camera_images')
        )
        ->filter(fn ($path) => preg_match('/\.(jpg|jpeg|png)$/i', $path))
        ->map(fn ($path) => basename($path))
        ->values()
        ->toArray();

        if (empty($localImages)) {
            echo "❌ No images found in camera_images folder.\n";
            return;
        }

        // Create 12 motion detection records
        for ($i = 0; $i < 12; $i++) {

            $timestamp = now()
                ->subDays(random_int(0, 30))
                ->subHours(random_int(0, 23));

            $filename = $localImages[$i % count($localImages)];
            $imagePath = 'camera_images/' . $filename;

            $fileSize = Storage::disk('public')->size($imagePath);

            $triggerTypes = ['motion', 'manual', 'auto', 'alert'];
            $triggerType = $triggerTypes[array_rand($triggerTypes)];

            $image = CameraImage::create([
                'user_id' => $user->id,
                'device_id' => $device->id,
                'filename' => $filename,
                'image_path' => $imagePath,
                'mime_type' => 'image/jpeg',
                'file_size' => $fileSize,
                'trigger_type' => $triggerType,
                'caption' => 'Motion detected at ' . $timestamp->format('M d, Y H:i:s'),
                'is_favorite' => $i % 4 === 0,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            $status = $i % 2 === 0 ? 'active' : 'resolved';

            Alert::create([
                'user_id' => $user->id,
                'device_id' => $device->id,
                'type' => 'motion_detected',
                'severity' => 'warning',
                'status' => $status,
                'message' => "🏃 Motion detected in {$device->name} at {$device->location}",
                'reading_value' => '1',
                'threshold_value' => '0',
                'camera_image_id' => $image->id,
                'resolved_at' => $status === 'resolved'
                    ? $timestamp->copy()->addMinutes(random_int(5, 60))
                    : null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            echo "✓ Created alert using {$filename}\n";
        }

        echo "\n✅ Motion detection data seeded successfully!\n";
    }
}
