<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;
use Auth;

class AlertController extends Controller
{
    public function index()
    {
        $alerts = Auth::user()->alerts()
            ->with('device')
            ->latest()
            ->paginate(20);

        $stats = [
            'total' => Auth::user()->alerts()->count(),
            'active' => Auth::user()->alerts()->where('status', 'active')->count(),
            'critical' => Auth::user()->alerts()->where('severity', 'critical')->count(),
            'acknowledged' => Auth::user()->alerts()->where('status', 'acknowledged')->count(),
        ];

        return view('alerts.index', [
            'alerts' => $alerts,
            'stats' => $stats,
        ]);
    }

    public function show(Alert $alert)
    {
        if ($alert->user_id !== Auth::id()) {
            abort(403);
        }

        // Mark as acknowledged
        if ($alert->status === 'active') {
            $alert->acknowledge();
        }

        return view('alerts.show', ['alert' => $alert]);
    }

    public function acknowledge(Alert $alert)
    {
        if ($alert->user_id !== Auth::id()) {
            abort(403);
        }

        $alert->acknowledge();
        return back()->with('success', 'Alert acknowledged');
    }

    public function resolve(Alert $alert)
    {
        if ($alert->user_id !== Auth::id()) {
            abort(403);
        }

        $alert->resolve();
        return back()->with('success', 'Alert resolved');
    }
}