<?php

namespace Database\Seeders;

use App\Models\CameraImage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class TestImagesSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $device = $user->devices()->first();

        if (!$device) {
            echo "No device found!\n";
            return;
        }

        // Create storage directory if not exists
        if (!Storage::exists('public/camera_images')) {
            Storage::makeDirectory('public/camera_images');
        }

        // Create fake images from a URL or use a local image
        $imageUrl = 'https://via.placeholder.com/320x240/3b82f6/ffffff?text=ESP32+Camera';

        for ($i = 0; $i < 12; $i++) {
            // Generate a unique filename
            $filename = 'test-image-' . ($i + 1) . '-' . time() . '.jpg';

            // Download and save image
            try {
                $imageContent = file_get_contents($imageUrl);
                Storage::put('public/camera_images/' . $filename, $imageContent);

                // Create database record
                CameraImage::create([
                    'user_id' => $user->id,
                    'device_id' => $device->id,
                    'filename' => $filename,
                    'image_path' => 'camera_images/' . $filename,
                    'mime_type' => 'image/jpeg',
                    'file_size' => strlen($imageContent),
                    'trigger_type' => $i % 3 == 0 ? 'manual' : ($i % 3 == 1 ? 'auto' : 'alert'),
                    'caption' => 'Test image ' . ($i + 1),
                    'is_favorite' => $i % 4 == 0,
                    'created_at' => now()->subHours(12 - $i),
                    'updated_at' => now()->subHours(12 - $i),
                ]);

                echo "✓ Created image " . ($i + 1) . "\n";
            } catch (\Exception $e) {
                echo "✗ Failed to create image " . ($i + 1) . ": " . $e->getMessage() . "\n";
            }
        }

        echo "\n✅ Test images created successfully!\n";
    }
}