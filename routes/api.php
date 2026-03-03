<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes — ESP32 / IoT Device Communication
|--------------------------------------------------------------------------
|
| All routes here are stateless (no session/CSRF required).
| Your ESP32 sends JSON or multipart/form-data to these endpoints.
|
| Base URL:  https://your-domain.com/api
|
| Example ESP32 POST:
|   POST /api/sensor-data
|   Content-Type: application/json
|   Body: {"device_id":"AA:BB:CC:DD:EE:FF","temperature":28.5,"humidity":62.1,"gas_level":320}
|
*/

// ──────────────────────────────────────────────
// DEVICE DATA — Inbound from ESP32
// ──────────────────────────────────────────────

// Store temperature / humidity / gas readings
Route::post('/sensor-data', [ApiController::class, 'storeSensorData']);

// Report a PIR motion detection event
Route::post('/motion-detected', [ApiController::class, 'storeMotionDetection']);

// Upload a JPEG image from ESP32-CAM
Route::post('/upload-image', [ApiController::class, 'uploadImage']);


// ──────────────────────────────────────────────
// DEVICE QUERY — Outbound to ESP32 / dashboard
// ──────────────────────────────────────────────

// Get the most recent sensor reading for a device
Route::get('/device/{deviceId}/latest-reading', [ApiController::class, 'getLatestReading']);

// Get historical readings (chart data)
// ?hours=24  (default 24, max 720 = 30 days)
Route::get('/device/{deviceId}/readings', [ApiController::class, 'getReadingsHistory']);


// ──────────────────────────────────────────────
// HEALTH CHECK
// ──────────────────────────────────────────────

Route::get('/ping', function () {
    return response()->json([
        'status'      => 'ok',
        'service'     => 'HomeGuard API',
        'server_time' => now()->toIso8601String(),
    ]);
});
