<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\CameraController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;

// Public
Route::get('/', fn() => view('welcome'))->name('welcome');
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login']);
Route::get('/register',   [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',  [AuthController::class, 'register']);

// Authenticated
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard',        [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/device/{device}',  [DashboardController::class, 'device'])->name('device.show');
    Route::get('/device/{device}/chart-data', [DashboardController::class, 'chartData'])->name('device.chart');

    // Devices
    Route::get('/devices',               [DeviceController::class, 'index'])->name('devices.index');
    Route::get('/devices/create',        [DeviceController::class, 'create'])->name('devices.create');
    Route::post('/devices',              [DeviceController::class, 'store'])->name('devices.store');
    Route::get('/devices/{device}',      [DeviceController::class, 'show'])->name('devices.show');
    Route::get('/devices/{device}/edit', [DeviceController::class, 'edit'])->name('devices.edit');
    Route::put('/devices/{device}',      [DeviceController::class, 'update'])->name('devices.update');
    Route::delete('/devices/{device}',   [DeviceController::class, 'destroy'])->name('devices.destroy');
    Route::put('/devices/{device}/thresholds',[DeviceController::class, 'updateThresholds'])->name('devices.updateThresholds');

    // Alerts
    Route::get('/alerts',                      [AlertController::class, 'index'])->name('alerts.index');
    Route::get('/alerts/{alert}',              [AlertController::class, 'show'])->name('alerts.show');
    Route::post('/alerts/{alert}/resolve',     [AlertController::class, 'resolve'])->name('alerts.resolve');
    Route::post('/alerts/{alert}/acknowledge', [AlertController::class, 'acknowledge'])->name('alerts.acknowledge');

    // Camera
    Route::get('/camera/{device}/gallery',  [CameraController::class, 'gallery'])->name('camera.gallery');
    Route::get('/camera/image/{image}',     [CameraController::class, 'view'])->name('camera.view');
    Route::post('/camera/{image}/favorite', [CameraController::class, 'toggleFavorite'])->name('camera.favorite');
    Route::delete('/camera/{image}',        [CameraController::class, 'delete'])->name('camera.delete');
    Route::post('/camera/{image}',          [CameraController::class, 'delete']);

    // Profile
    Route::get('/profile',           [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile',           [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password',  [ProfileController::class, 'changePassword'])->name('profile.password');
    Route::delete('/profile',        [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Settings
    Route::get('/settings',                [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/general',       [SettingsController::class, 'updateGeneral'])->name('settings.general');
    Route::post('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications');
});
