<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| These routes are for ESP32 and external device communication.
| They are stateless and use the api middleware group.
|
*/

// ================================
// DEVICE DATA ROUTES
// ================================

// Store temperature, humidity, gas data
Route::post('/sensor-data', [ApiController::class, 'storeSensorData']);

// Store motion detection event
Route::post('/motion-detected', [ApiController::class, 'storeMotionDetection']);

// Upload ESP32-CAM image
Route::post('/upload-image', [ApiController::class, 'uploadImage']);


// ================================
// DEVICE QUERY ROUTES
// ================================

// Get latest sensor reading for a device
Route::get('/device/{device}/latest-reading', [ApiController::class, 'getLatestReading']);


// ================================
// OPTIONAL: Test Route
// ================================

Route::get('/ping', function () {
    return response()->json([
        'status' => 'API is working',
        'timestamp' => now()
    ]);
});