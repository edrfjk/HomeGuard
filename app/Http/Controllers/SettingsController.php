<?php

namespace App\Http\Controllers;

use App\Models\NotificationPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $prefs = $this->getOrCreatePrefs($user);

        return view('settings.index', [
            'notificationPrefs' => $prefs,
            'userTimezone'      => $user->timezone ?? 'UTC',
        ]);
    }

    /**
     * Save timezone.
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'timezone' => 'required|timezone',
        ]);

        Auth::user()->update(['timezone' => $validated['timezone']]);

        return back()->with('success', 'Timezone saved. All timestamps now use ' . $validated['timezone'] . '.');
    }

    /**
     * Save notification preferences — all 5 toggles written to the DB.
     */
    public function updateNotifications(Request $request)
    {
        $user  = Auth::user();
        $prefs = $this->getOrCreatePrefs($user);

        $prefs->update([
            'critical_alerts' => $request->boolean('critical_alerts'),
            'warning_alerts'  => $request->boolean('warning_alerts'),
            'device_status'   => $request->boolean('device_status'),
            'push_enabled'    => $request->boolean('push_enabled'),
            'email_enabled'   => $request->boolean('email_enabled'),
        ]);

        return back()->with('success', 'Notification preferences saved.');
    }

    // ── helpers ──────────────────────────────────────────────

    private function getOrCreatePrefs($user): NotificationPreference
    {
        return NotificationPreference::firstOrCreate(
            ['user_id' => $user->id],
            [
                'critical_alerts' => true,
                'warning_alerts'  => true,
                'device_status'   => false,
                'push_enabled'    => true,
                'email_enabled'   => true,
            ]
        );
    }
}
