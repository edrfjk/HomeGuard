<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\CameraController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/device/{device}', [DashboardController::class, 'device'])->name('device.detail');

    // Devices
    Route::resource('devices', DeviceController::class);
    Route::put('/devices/{device}/thresholds', [DeviceController::class, 'updateThresholds'])->name('devices.updateThresholds');

    // Alerts
    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::get('/alerts/{alert}', [AlertController::class, 'show'])->name('alerts.show');
    Route::post('/alerts/{alert}/acknowledge', [AlertController::class, 'acknowledge'])->name('alerts.acknowledge');
    Route::post('/alerts/{alert}/resolve', [AlertController::class, 'resolve'])->name('alerts.resolve');

    // Camera
    Route::get('/camera/{device}/gallery', [CameraController::class, 'gallery'])->name('camera.gallery');
    Route::get('/camera/{image}', [CameraController::class, 'view'])->name('camera.view');
    Route::post('/camera/{image}/favorite', [CameraController::class, 'toggleFavorite'])->name('camera.favorite');
    Route::delete('/camera/{image}', [CameraController::class, 'delete'])->name('camera.delete');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// API routes for ESP32 to send data
Route::post('/api/sensor-data', [App\Http\Controllers\ApiController::class, 'storeSensorData']);
Route::post('/api/upload-image', [App\Http\Controllers\ApiController::class, 'uploadImage']);


// ... existing code ...

Route::middleware('auth')->group(function () {
    // ... existing routes ...

    // Camera Gallery
    Route::get('/camera/{device}/gallery', [CameraController::class, 'gallery'])->name('camera.gallery');
    Route::get('/camera/{image}', [CameraController::class, 'view'])->name('camera.view');
    Route::post('/camera/{image}/favorite', [CameraController::class, 'toggleFavorite'])->name('camera.favorite');
    Route::delete('/camera/{image}', [CameraController::class, 'delete'])->name('camera.delete');

    // Profile
    Route::get('/profile', function () {
        return view('profile.index');
    })->name('profile');

    // Settings
    Route::get('/settings', function () {
        return view('settings.index');
    })->name('settings');
});