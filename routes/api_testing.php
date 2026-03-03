<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::get('/ping', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'HomeGuard API',
        'server_time' => now()->toIso8601String(),
    ]);
});