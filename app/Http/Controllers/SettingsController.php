<?php

namespace App\Http\Controllers;

use App\Models\NotificationPreference;
use Illuminate\Http\Request;
use Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notificationPrefs = NotificationPreference::where('user_id', $user->id)->first();
        
        if (!$notificationPrefs) {
            $notificationPrefs = NotificationPreference::create([
                'user_id' => $user->id,
                'critical_alerts' => true,
                'warning_alerts' => true,
                'device_status' => false,
                'push_enabled' => true,
                'email_enabled' => true,
            ]);
        }

        return view('settings.index', [
            'notificationPrefs' => $notificationPrefs,
        ]);
    }

    public function updateGeneral(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'timezone' => 'required|timezone',
        ]);

        $user->update([
            'timezone' => $validated['timezone'],
        ]);

        return back()->with('success', 'Timezone updated! All future timestamps will use ' . $validated['timezone']);
    }

    public function updateNotifications(Request $request)
    {
        $user = Auth::user();

        $notificationPrefs = NotificationPreference::where('user_id', $user->id)->first();
        
        if (!$notificationPrefs) {
            $notificationPrefs = new NotificationPreference(['user_id' => $user->id]);
        }

        $notificationPrefs->update([
            'critical_alerts' => $request->has('critical_alerts'),
            'warning_alerts' => $request->has('warning_alerts'),
            'device_status' => $request->has('device_status'),
        ]);

        return back()->with('success', 'Notification preferences updated!');
    }
}