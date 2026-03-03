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
        $devices = $user->devices()->where('is_active', true)->orderBy('name')->get();

        // Get latest readings for each device (single query batch)
        $latestReadings = [];
        foreach ($devices as $device) {
            $latestReadings[$device->id] = $device->latestReading();
        }

        // Device statistics
        $totalDevices  = $devices->count();
        $onlineDevices = $devices->where('status', 'online')->count();

        // Alert statistics
        $stats = [
            'total'    => $user->alerts()->count(),
            'active'   => $user->alerts()->where('status', 'active')->count(),
            'critical' => $user->alerts()->where('severity', 'critical')->where('status', 'active')->count(),
            'resolved' => $user->alerts()->where('status', 'resolved')->count(),
        ];

        return view('dashboard.index', compact(
            'devices',
            'latestReadings',
            'totalDevices',
            'onlineDevices',
            'stats'
        ));
    }

    public function device(Device $device)
    {
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }

        $readings24h  = $device->readingsLast24Hours();
        $alerts       = $device->alerts()->with('sensorReading')->latest()->take(20)->get();
        $threshold    = $device->safetyThreshold;
        $latestReading = $device->latestReading();
        $latestImage  = $device->latestCameraImage();

        return view('dashboard.device', compact(
            'device',
            'readings24h',
            'alerts',
            'threshold',
            'latestReading',
            'latestImage'
        ));
    }

    /**
     * Return JSON chart data for a device (used by JS on device detail page)
     * GET /device/{device}/chart-data?hours=24
     */
    public function chartData(Device $device, Request $request)
    {
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }

        $hours = min((int) $request->get('hours', 24), 720);

        $readings = $device->sensorReadings()
            ->where('created_at', '>=', now()->subHours($hours))
            ->orderBy('created_at', 'asc')
            ->select(['temperature', 'humidity', 'gas_level', 'created_at'])
            ->get()
            ->map(fn($r) => [
                'time'        => $r->created_at->format('M d, H:i'),
                'temperature' => (float) $r->temperature,
                'humidity'    => (float) $r->humidity,
                'gas_level'   => (float) $r->gas_level,
            ]);

        return response()->json([
            'readings' => $readings,
            'count'    => $readings->count(),
        ]);
    }
}
