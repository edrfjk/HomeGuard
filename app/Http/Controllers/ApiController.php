<?php

namespace App\Http\Controllers;

use App\Models\SensorReading;
use App\Models\CameraImage;
use App\Models\Alert;
use App\Models\Device;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    // Store sensor data from ESP32
    public function storeSensorData(Request $request)
    {
        $device = Device::where('device_id', $request->device_id)->first();
        
        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        // Store sensor reading
        $reading = SensorReading::create([
            'user_id' => $device->user_id,
            'device_id' => $device->id,
            'temperature' => $request->temperature,
            'humidity' => $request->humidity,
            'gas_level' => $request->gas_level,
        ]);

        // Update device status
        $device->update([
            'status' => 'online',
            'last_seen' => now()
        ]);

        // Check thresholds and create alerts
        $this->checkThresholdsAndCreateAlerts($device, $reading);

        return response()->json(['success' => true, 'reading_id' => $reading->id]);
    }

    // Handle motion detection from PIR sensor
    public function storeMotionDetection(Request $request)
    {
        $device = Device::where('device_id', $request->device_id)->first();
        
        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        // Create motion alert
        $alert = Alert::create([
            'user_id' => $device->user_id,
            'device_id' => $device->id,
            'type' => 'motion_detected',
            'severity' => 'warning', // Motion is warning level
            'status' => 'active',
            'message' => "Motion detected in {$device->name} at {$device->location}",
            'reading_value' => '1',
            'threshold_value' => '0',
        ]);

        // Update device status
        $device->update([
            'status' => 'online',
            'last_seen' => now()
        ]);

        return response()->json([
            'success' => true, 
            'alert_id' => $alert->id,
            'message' => 'Motion alert created'
        ]);
    }

    // Upload image from ESP32-CAM
    public function uploadImage(Request $request)
    {
        $device = Device::where('device_id', $request->device_id)->first();
        
        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        // Store image
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('camera_images', 'public');

            $image = CameraImage::create([
                'user_id' => $device->user_id,
                'device_id' => $device->id,
                'image_path' => $path,
                'trigger_type' => $request->trigger_type ?? 'manual', // manual, motion, alert
                'caption' => $request->caption ?? null,
                'file_size' => $request->file('image')->getSize(),
            ]);

            // If triggered by motion, link to motion alert
            if ($request->trigger_type === 'motion' && $request->alert_id) {
                Alert::find($request->alert_id)->update([
                    'camera_image_id' => $image->id
                ]);
            }

            return response()->json([
                'success' => true,
                'image_id' => $image->id,
                'path' => $path
            ]);
        }

        return response()->json(['error' => 'No image provided'], 400);
    }

    // Get latest reading
    public function getLatestReading(Device $device)
    {
        $reading = $device->latestReading();
        return response()->json($reading);
    }

    // Check thresholds and create alerts
    private function checkThresholdsAndCreateAlerts(Device $device, SensorReading $reading)
    {
        $threshold = $device->safetyThreshold;

        // Temperature check
        if ($reading->temperature >= $threshold->temp_critical) {
            Alert::create([
                'user_id' => $device->user_id,
                'device_id' => $device->id,
                'type' => 'temperature_critical',
                'severity' => 'critical',
                'status' => 'active',
                'message' => "🚨 CRITICAL: Temperature is {$reading->temperature}°C in {$device->name}",
                'reading_value' => $reading->temperature,
                'threshold_value' => $threshold->temp_critical,
                'sensor_reading_id' => $reading->id,
            ]);
        } elseif ($reading->temperature >= $threshold->temp_warning) {
            Alert::create([
                'user_id' => $device->user_id,
                'device_id' => $device->id,
                'type' => 'temperature_warning',
                'severity' => 'warning',
                'status' => 'active',
                'message' => "⚠️ WARNING: Temperature is {$reading->temperature}°C in {$device->name}",
                'reading_value' => $reading->temperature,
                'threshold_value' => $threshold->temp_warning,
                'sensor_reading_id' => $reading->id,
            ]);
        }

        // Humidity check
        if ($reading->humidity >= $threshold->humidity_critical) {
            Alert::create([
                'user_id' => $device->user_id,
                'device_id' => $device->id,
                'type' => 'humidity_critical',
                'severity' => 'critical',
                'status' => 'active',
                'message' => "🚨 CRITICAL: Humidity is {$reading->humidity}% in {$device->name}",
                'reading_value' => $reading->humidity,
                'threshold_value' => $threshold->humidity_critical,
                'sensor_reading_id' => $reading->id,
            ]);
        } elseif ($reading->humidity >= $threshold->humidity_warning) {
            Alert::create([
                'user_id' => $device->user_id,
                'device_id' => $device->id,
                'type' => 'humidity_warning',
                'severity' => 'warning',
                'status' => 'active',
                'message' => "⚠️ WARNING: Humidity is {$reading->humidity}% in {$device->name}",
                'reading_value' => $reading->humidity,
                'threshold_value' => $threshold->humidity_warning,
                'sensor_reading_id' => $reading->id,
            ]);
        }

        // Gas check
        if ($reading->gas_level >= $threshold->gas_critical) {
            Alert::create([
                'user_id' => $device->user_id,
                'device_id' => $device->id,
                'type' => 'gas_critical',
                'severity' => 'critical',
                'status' => 'active',
                'message' => "🚨 CRITICAL: Gas level is {$reading->gas_level} PPM in {$device->name}",
                'reading_value' => $reading->gas_level,
                'threshold_value' => $threshold->gas_critical,
                'sensor_reading_id' => $reading->id,
            ]);
        } elseif ($reading->gas_level >= $threshold->gas_warning) {
            Alert::create([
                'user_id' => $device->user_id,
                'device_id' => $device->id,
                'type' => 'gas_warning',
                'severity' => 'warning',
                'status' => 'active',
                'message' => "⚠️ WARNING: Gas level is {$reading->gas_level} PPM in {$device->name}",
                'reading_value' => $reading->gas_level,
                'threshold_value' => $threshold->gas_warning,
                'sensor_reading_id' => $reading->id,
            ]);
        }
    }
}