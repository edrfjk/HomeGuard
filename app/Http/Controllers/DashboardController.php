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
        
        // Get all active devices of the user
        $devices = $user->devices()->where('is_active', true)->get();
        
        // Get latest readings for each device
        $latestReadings = [];
        foreach ($devices as $device) {
            $latestReadings[$device->id] = $device->latestReading();
        }

        // Device statistics
        $totalDevices = $devices->count();
        $onlineDevices = $devices->where('status', 'online')->count();

        // Alert statistics
        $stats = [
            'total' => $user->alerts()->count(),
            'active' => $user->alerts()->where('status', 'active')->count(),
            'critical' => $user->alerts()->where('severity', 'critical')->where('status', 'active')->count(),
            'resolved' => $user->alerts()->where('status', 'resolved')->count(),
        ];

        // Recent alerts - show only the latest 10
        $recentAlerts = $user->alerts()
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.index', [
            'devices' => $devices,
            'latestReadings' => $latestReadings,
            'totalDevices' => $totalDevices,
            'onlineDevices' => $onlineDevices,
            'stats' => $stats,           // pass stats for dashboard cards
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
        $threshold = $device->safetyThreshold; // Device safety threshold
        $latestReading = $device->latestReading();
        $latestImage = $device->latestCameraImage(); // Latest captured image

        return view('dashboard.device', [
            'device' => $device,
            'readings24h' => $readings24h,
            'alerts' => $alerts,
            'threshold' => $threshold,
            'latestReading' => $latestReading,
            'latestImage' => $latestImage,
        ]);
    }
}
