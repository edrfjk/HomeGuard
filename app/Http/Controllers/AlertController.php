<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;
use Auth;

class AlertController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = $user->alerts()->latest();
        
        // Get filter values
        $deviceFilter = request('device', 'all');
        $statusFilter = request('status', 'all');
        $severityFilter = request('severity', 'all');
        $dateFilter = request('date', 'all');
        $typeFilter = request('type', 'all');
        
        // Apply device filter
        if ($deviceFilter !== 'all') {
            $query = $query->where('device_id', $deviceFilter);
        }
        
        // Apply status filter
        if ($statusFilter === 'active') {
            $query = $query->where('status', 'active');
        } elseif ($statusFilter === 'resolved') {
            $query = $query->where('status', 'resolved');
        }
        
        // Apply severity filter
        if ($severityFilter === 'critical') {
            $query = $query->where('severity', 'critical');
        } elseif ($severityFilter === 'warning') {
            $query = $query->where('severity', 'warning');
        }
        
        // Apply date filter
        if ($dateFilter === 'today') {
            $query = $query->whereDate('created_at', today());
        } elseif ($dateFilter === 'week') {
            $query = $query->whereDate('created_at', '>=', now()->subWeek());
        } elseif ($dateFilter === 'month') {
            $query = $query->whereDate('created_at', '>=', now()->subMonth());
        }
        
        // Apply type filter
        if ($typeFilter !== 'all') {
            $query = $query->where('type', $typeFilter);
        }
        
        $alerts = $query->paginate(15);
        
        // Get stats
        $stats = [
            'total' => $user->alerts()->count(),
            'active' => $user->alerts()->where('status', 'active')->count(),
            'critical' => $user->alerts()->where('severity', 'critical')->where('status', 'active')->count(),
            'resolved' => $user->alerts()->where('status', 'resolved')->count(),
        ];

        return view('alerts.index', [
            'alerts' => $alerts,
            'stats' => $stats,
            'deviceFilter' => $deviceFilter,
            'statusFilter' => $statusFilter,
            'severityFilter' => $severityFilter,
            'dateFilter' => $dateFilter,
            'typeFilter' => $typeFilter,
        ]);
    }

    public function show(Alert $alert)
    {
        if ($alert->user_id !== Auth::id()) {
            abort(403);
        }

        return view('alerts.show', ['alert' => $alert]);
    }

    public function acknowledge(Alert $alert)
    {
        if ($alert->user_id !== Auth::id()) {
            abort(403);
        }

        $alert->update(['status' => 'acknowledged']);
        return back()->with('success', 'Alert acknowledged!');
    }

    public function resolve(Alert $alert)
    {
        if ($alert->user_id !== Auth::id()) {
            abort(403);
        }

        $alert->update([
            'status' => 'resolved',
            'resolved_at' => now()
        ]);

        return back()->with('success', 'Alert resolved!');
    }
}