<?php

namespace App\Http\Controllers;

use App\Models\SensorReading;
use App\Models\CameraImage;
use App\Models\Alert;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    /**
     * Store sensor data from ESP32
     * POST /api/sensor-data
     * Body: { device_id, temperature, humidity, gas_level, signal_strength? }
     */
    public function storeSensorData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id'        => 'required|string',
            'temperature'      => 'required|numeric|between:-50,150',
            'humidity'         => 'required|numeric|between:0,100',
            'gas_level'        => 'required|numeric|min:0',
            'signal_strength'  => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error'   => 'Validation failed',
                'details' => $validator->errors(),
            ], 422);
        }

        $device = Device::where('device_id', $request->device_id)->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'error'   => 'Device not found. Please register device first.',
                'device_id' => $request->device_id,
            ], 404);
        }

        if (!$device->is_active) {
            return response()->json([
                'success' => false,
                'error'   => 'Device is disabled.',
            ], 403);
        }

        // Store sensor reading
        $reading = SensorReading::create([
            'user_id'         => $device->user_id,
            'device_id'       => $device->id,
            'temperature'     => round($request->temperature, 2),
            'humidity'        => round($request->humidity, 2),
            'gas_level'       => round($request->gas_level, 2),
            'signal_strength' => $request->signal_strength,
        ]);

        // Update device status & IP
        $device->update([
            'status'     => 'online',
            'last_seen'  => now(),
            'ip_address' => $request->ip(),
        ]);

        // Check thresholds and create alerts
        $alerts = $this->checkThresholdsAndCreateAlerts($device, $reading);

        return response()->json([
            'success'    => true,
            'reading_id' => $reading->id,
            'alerts_created' => count($alerts),
            'server_time'    => now()->toIso8601String(),
        ]);
    }

    /**
     * Handle motion detection from PIR sensor
     * POST /api/motion-detected
     * Body: { device_id, location? }
     */
    public function storeMotionDetection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
            'location'  => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error'   => 'Validation failed',
                'details' => $validator->errors(),
            ], 422);
        }

        $device = Device::where('device_id', $request->device_id)->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'error'   => 'Device not found.',
            ], 404);
        }

        $location = $request->location ?? $device->location;

        // Create motion alert
        $alert = Alert::create([
            'user_id'         => $device->user_id,
            'device_id'       => $device->id,
            'type'            => 'motion_detected',
            'severity'        => 'warning',
            'status'          => 'active',
            'message'         => "Motion detected at {$location} ({$device->name})",
            'reading_value'   => '1',
            'threshold_value' => '0',
        ]);

        // Update device
        $device->update([
            'status'    => 'online',
            'last_seen' => now(),
        ]);

        return response()->json([
            'success'     => true,
            'alert_id'    => $alert->id,
            'message'     => 'Motion alert created',
            'server_time' => now()->toIso8601String(),
        ]);
    }

    /**
     * Upload image from ESP32-CAM
     * POST /api/upload-image
     * Body: multipart/form-data { device_id, image, trigger_type?, alert_id?, caption? }
     */
    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id'    => 'required|string',
            'image'        => 'required|file|mimes:jpg,jpeg,png|max:5120', // 5MB max
            'trigger_type' => 'nullable|in:manual,motion,alert',
            'alert_id'     => 'nullable|integer',
            'caption'      => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error'   => 'Validation failed',
                'details' => $validator->errors(),
            ], 422);
        }

        $device = Device::where('device_id', $request->device_id)->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'error'   => 'Device not found.',
            ], 404);
        }

        if (!$request->hasFile('image') || !$request->file('image')->isValid()) {
            return response()->json([
                'success' => false,
                'error'   => 'Invalid image file.',
            ], 400);
        }

        // Store image with date-organized path
        $path = $request->file('image')->store(
            'camera_images/' . now()->format('Y/m/d'),
            'public'
        );

        $image = CameraImage::create([
            'user_id'      => $device->user_id,
            'device_id'    => $device->id,
            'image_path'   => $path,
            'trigger_type' => $request->trigger_type ?? 'manual',
            'caption'      => $request->caption,
            'file_size'    => $request->file('image')->getSize(),
        ]);

        // Link image to a motion alert if provided
        if ($request->trigger_type === 'motion' && $request->alert_id) {
            Alert::where('id', $request->alert_id)
                 ->where('device_id', $device->id)
                 ->update(['camera_image_id' => $image->id]);
        }

        // Update device
        $device->update([
            'status'    => 'online',
            'last_seen' => now(),
        ]);

        return response()->json([
            'success'     => true,
            'image_id'    => $image->id,
            'path'        => $path,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    /**
     * Get latest sensor reading for a device
     * GET /api/device/{device_id}/latest-reading
     */
    public function getLatestReading(Request $request, $deviceId)
    {
        $device = Device::where('device_id', $deviceId)->first();

        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        $reading = $device->latestReading();

        if (!$reading) {
            return response()->json([
                'device_id'   => $deviceId,
                'status'      => $device->status,
                'reading'     => null,
                'message'     => 'No readings yet',
                'server_time' => now()->toIso8601String(),
            ]);
        }

        return response()->json([
            'device_id'   => $deviceId,
            'device_name' => $device->name,
            'status'      => $device->status,
            'reading' => [
                'id'              => $reading->id,
                'temperature'     => $reading->temperature,
                'humidity'        => $reading->humidity,
                'gas_level'       => $reading->gas_level,
                'signal_strength' => $reading->signal_strength,
                'recorded_at'     => $reading->created_at->toIso8601String(),
            ],
            'server_time' => now()->toIso8601String(),
        ]);
    }

    /**
     * Get device readings history (for chart data)
     * GET /api/device/{device_id}/readings?hours=24
     */
    public function getReadingsHistory(Request $request, $deviceId)
    {
        $device = Device::where('device_id', $deviceId)->first();

        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        $hours = min((int) $request->get('hours', 24), 720); // max 30 days

        $readings = $device->sensorReadings()
            ->where('created_at', '>=', now()->subHours($hours))
            ->orderBy('created_at', 'asc')
            ->select(['id', 'temperature', 'humidity', 'gas_level', 'created_at'])
            ->get()
            ->map(fn($r) => [
                'time'        => $r->created_at->toIso8601String(),
                'temperature' => $r->temperature,
                'humidity'    => $r->humidity,
                'gas_level'   => $r->gas_level,
            ]);

        return response()->json([
            'device_id'   => $deviceId,
            'hours'       => $hours,
            'count'       => $readings->count(),
            'readings'    => $readings,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    // =========================================================
    // Private Helpers
    // =========================================================

    private function checkThresholdsAndCreateAlerts(Device $device, SensorReading $reading): array
    {
        $threshold = $device->safetyThreshold;
        $created   = [];

        if (!$threshold) {
            return $created;
        }

        // Temperature checks
        if ($reading->temperature >= $threshold->temp_critical) {
            $created[] = Alert::create([
                'user_id'           => $device->user_id,
                'device_id'         => $device->id,
                'type'              => 'temperature_critical',
                'severity'          => 'critical',
                'status'            => 'active',
                'message'           => "CRITICAL: Temperature is {$reading->temperature}°C at {$device->name} ({$device->location})",
                'reading_value'     => $reading->temperature,
                'threshold_value'   => $threshold->temp_critical,
                'sensor_reading_id' => $reading->id,
            ]);
        } elseif ($reading->temperature >= $threshold->temp_warning) {
            $created[] = Alert::create([
                'user_id'           => $device->user_id,
                'device_id'         => $device->id,
                'type'              => 'temperature_warning',
                'severity'          => 'warning',
                'status'            => 'active',
                'message'           => "WARNING: Temperature is {$reading->temperature}°C at {$device->name} ({$device->location})",
                'reading_value'     => $reading->temperature,
                'threshold_value'   => $threshold->temp_warning,
                'sensor_reading_id' => $reading->id,
            ]);
        }

        // Humidity checks
        if ($reading->humidity >= $threshold->humidity_critical) {
            $created[] = Alert::create([
                'user_id'           => $device->user_id,
                'device_id'         => $device->id,
                'type'              => 'humidity_critical',
                'severity'          => 'critical',
                'status'            => 'active',
                'message'           => "CRITICAL: Humidity is {$reading->humidity}% at {$device->name}",
                'reading_value'     => $reading->humidity,
                'threshold_value'   => $threshold->humidity_critical,
                'sensor_reading_id' => $reading->id,
            ]);
        } elseif ($reading->humidity >= $threshold->humidity_warning) {
            $created[] = Alert::create([
                'user_id'           => $device->user_id,
                'device_id'         => $device->id,
                'type'              => 'humidity_warning',
                'severity'          => 'warning',
                'status'            => 'active',
                'message'           => "WARNING: Humidity is {$reading->humidity}% at {$device->name}",
                'reading_value'     => $reading->humidity,
                'threshold_value'   => $threshold->humidity_warning,
                'sensor_reading_id' => $reading->id,
            ]);
        }

        // Gas checks
        if ($reading->gas_level >= $threshold->gas_critical) {
            $created[] = Alert::create([
                'user_id'           => $device->user_id,
                'device_id'         => $device->id,
                'type'              => 'gas_critical',
                'severity'          => 'critical',
                'status'            => 'active',
                'message'           => "CRITICAL: Gas level is {$reading->gas_level} PPM at {$device->name}",
                'reading_value'     => $reading->gas_level,
                'threshold_value'   => $threshold->gas_critical,
                'sensor_reading_id' => $reading->id,
            ]);
        } elseif ($reading->gas_level >= $threshold->gas_warning) {
            $created[] = Alert::create([
                'user_id'           => $device->user_id,
                'device_id'         => $device->id,
                'type'              => 'gas_warning',
                'severity'          => 'warning',
                'status'            => 'active',
                'message'           => "WARNING: Gas level is {$reading->gas_level} PPM at {$device->name}",
                'reading_value'     => $reading->gas_level,
                'threshold_value'   => $threshold->gas_warning,
                'sensor_reading_id' => $reading->id,
            ]);
        }

        return $created;
    }
}
