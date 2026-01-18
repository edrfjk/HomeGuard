<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\SensorReading;
use App\Models\Alert;
use App\Models\CameraImage;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    // ESP32 sends sensor data here
    public function storeSensorData(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|string',
            'temperature' => 'required|numeric',
            'humidity' => 'required|numeric',
            'gas_level' => 'required|numeric',
        ]);

        // Find device
        $device = Device::where('device_id', $validated['device_id'])->first();

        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        // Save sensor reading
        $reading = SensorReading::create([
            'user_id' => $device->user_id,
            'device_id' => $device->id,
            'temperature' => $validated['temperature'],
            'humidity' => $validated['humidity'],
            'gas_level' => $validated['gas_level'],
            'gas_status' => $this->getGasStatus($validated['gas_level'], $device),
        ]);

        // Update device last_seen
        $device->update(['last_seen' => now(), 'status' => 'online']);

        // Check thresholds and create alerts if needed
        $this->checkThresholds($reading, $device);

        return response()->json([
            'success' => true,
            'message' => 'Sensor data stored',
            'reading' => $reading,
        ]);
    }

    // ESP32 uploads camera image here
    public function uploadImage(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|string',
            'image' => 'required|image|max:5120', // 5MB max
            'trigger_type' => 'nullable|in:auto,manual,alert',
        ]);

        $device = Device::where('device_id', $validated['device_id'])->first();

        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        // Store image
        $file = $request->file('image');
        $filename = time() . '_' . $device->id . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('camera_images', $filename, 'public');

        $image = CameraImage::create([
            'user_id' => $device->user_id,
            'device_id' => $device->id,
            'image_path' => $path,
            'filename' => $filename,
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'trigger_type' => $validated['trigger_type'] ?? 'auto',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Image uploaded',
            'image' => $image,
        ]);
    }

    // Helper: Get gas status
    private function getGasStatus($gasLevel, $device)
    {
        $threshold = $device->safetyThreshold;

        if ($gasLevel >= $threshold->gas_critical) {
            return 'critical';
        }

        if ($gasLevel >= $threshold->gas_warning) {
            return 'warning';
        }

        return 'safe';
    }

    // Helper: Check thresholds and create alerts
    private function checkThresholds($reading, $device)
    {
        $threshold = $device->safetyThreshold;
        $alerts = [];

        // Check temperature
        if ($reading->temperature >= $threshold->temp_critical) {
            $alerts[] = [
                'type' => 'temperature_critical',
                'severity' => 'critical',
                'message' => 'CRITICAL: Temperature is ' . $reading->temperature . '°C',
            ];
        } elseif ($reading->temperature >= $threshold->temp_warning) {
            $alerts[] = [
                'type' => 'temperature_warning',
                'severity' => 'warning',
                'message' => 'WARNING: Temperature is ' . $reading->temperature . '°C',
            ];
        }

        // Check humidity
        if ($reading->humidity >= $threshold->humidity_critical) {
            $alerts[] = [
                'type' => 'humidity_critical',
                'severity' => 'critical',
                'message' => 'CRITICAL: Humidity is ' . $reading->humidity . '%',
            ];
        } elseif ($reading->humidity >= $threshold->humidity_warning) {
            $alerts[] = [
                'type' => 'humidity_warning',
                'severity' => 'warning',
                'message' => 'WARNING: Humidity is ' . $reading->humidity . '%',
            ];
        }

        // Check gas
        if ($reading->gas_level >= $threshold->gas_critical) {
            $alerts[] = [
                'type' => 'gas_critical',
                'severity' => 'critical',
                'message' => 'CRITICAL: Gas detected at ' . $reading->gas_level . ' PPM',
            ];
        } elseif ($reading->gas_level >= $threshold->gas_warning) {
            $alerts[] = [
                'type' => 'gas_warning',
                'severity' => 'warning',
                'message' => 'WARNING: Gas detected at ' . $reading->gas_level . ' PPM',
            ];
        }

        // Create alerts
        foreach ($alerts as $alertData) {
            Alert::create([
                'user_id' => $device->user_id,
                'device_id' => $device->id,
                'sensor_reading_id' => $reading->id,
                'type' => $alertData['type'],
                'severity' => $alertData['severity'],
                'message' => $alertData['message'],
                'reading_value' => $reading->temperature, // Or other relevant value
                'status' => 'active',
            ]);
        }
    }

    // Get latest reading for a device (for real-time updates)
public function getLatestReading($deviceId)
{
    $device = Device::findOrFail($deviceId);
    
    if ($device->user_id !== auth()->id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $reading = $device->latestReading();

    if (!$reading) {
        return response()->json(['error' => 'No readings yet'], 404);
    }

    return response()->json($reading);
}
}