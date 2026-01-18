<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\SensorReading;
use App\Models\Alert;
use Illuminate\Http\Request;
use Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $devices = $user->devices()->where('is_active', true)->get();
        
        // Get latest readings
        $latestReadings = [];
        foreach ($devices as $device) {
            $latestReadings[$device->id] = $device->latestReading();
        }

        // Statistics
        $totalDevices = $devices->count();
        $onlineDevices = $devices->where('status', 'online')->count();
        $criticalAlerts = $user->alerts()->where('severity', 'critical')->where('status', 'active')->count();
        
        // Recent alerts
        $recentAlerts = $user->alerts()->latest()->take(10)->get();

        return view('dashboard.index', [
            'devices' => $devices,
            'latestReadings' => $latestReadings,
            'totalDevices' => $totalDevices,
            'onlineDevices' => $onlineDevices,
            'criticalAlerts' => $criticalAlerts,
            'recentAlerts' => $recentAlerts,
        ]);
    }

    public function device($deviceId)
    {
        $device = Device::findOrFail($deviceId);
        
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }

        $readings24h = $device->readingsLast24Hours();
        $alerts = $device->alerts()->latest()->take(20)->get();
        $threshold = $device->safetyThreshold; // FIX: Explicitly get threshold
        $latestReading = $device->latestReading();
        $latestImage = $device->latestCameraImage(); // Get latest image

        return view('dashboard.device', [
            'device' => $device,
            'readings24h' => $readings24h,
            'alerts' => $alerts,
            'threshold' => $threshold,
            'latestReading' => $latestReading,
            'latestImage' => $latestImage, // Pass image to view
        ]);
    }
}