@extends('layouts.app')

@section('title', 'Settings — HomeGuard')
@section('page-title', 'Settings')
@section('page-subtitle', 'System configuration')

@section('content')
@php
    $section = request('section', 'general');
    $prefs   = $notificationPrefs;
@endphp

<div style="display:grid;grid-template-columns:200px 1fr;gap:18px;align-items:start;" id="settingsLayout">

    {{-- Sidebar --}}
    <div class="card" style="padding:6px;position:sticky;top:80px;">
        @php $navItems = [
            'general'       => ['icon'=>'fa-sliders',     'label'=>'General'],
            'notifications' => ['icon'=>'fa-bell',        'label'=>'Notifications'],
            'storage'       => ['icon'=>'fa-database',    'label'=>'Storage'],
            'about'         => ['icon'=>'fa-circle-info', 'label'=>'About'],
        ]; @endphp
        @foreach($navItems as $key => $nav)
        <a href="?section={{ $key }}"
           style="display:flex;align-items:center;gap:9px;padding:9px 12px;border-radius:7px;text-decoration:none;font-size:13px;font-weight:600;margin-bottom:2px;transition:all .15s;
           {{ $section===$key ? 'background:rgba(34,211,238,.1);color:var(--accent);' : 'color:var(--text-muted);' }}">
            <i class="fas {{ $nav['icon'] }}" style="width:14px;text-align:center;font-size:12px;"></i>
            {{ $nav['label'] }}
            @if($section===$key)
                <span style="width:4px;height:4px;border-radius:50%;background:var(--accent);margin-left:auto;"></span>
            @endif
        </a>
        @endforeach
    </div>

    {{-- Content --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Flash --}}
        @if(session('success'))
        <div style="background:rgba(52,211,153,.1);border:1px solid rgba(52,211,153,.25);color:var(--safe);padding:11px 16px;border-radius:9px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:8px;" class="fade-up">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        {{-- ── GENERAL ── --}}
        @if($section === 'general')
        <div class="card card-p fade-up">
            <div style="font-size:10px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-bottom:16px;"><i class="fas fa-sliders" style="margin-right:5px;color:var(--accent);"></i>GENERAL SETTINGS</div>

            <div style="background:rgba(34,211,238,.05);border:1px solid rgba(34,211,238,.12);border-radius:8px;padding:10px 14px;font-size:12px;color:var(--accent);margin-bottom:18px;">
                <i class="fas fa-circle-info" style="margin-right:5px;"></i>
                Timezone affects all sensor reading timestamps and chart labels.
            </div>

            <form method="POST" action="{{ route('settings.general') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">System Timezone</label>
                    <select name="timezone" class="form-control">
                        @foreach(\DateTimeZone::listIdentifiers() as $tz)
                            <option value="{{ $tz }}" {{ ($userTimezone ?? 'UTC') === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                        @endforeach
                    </select>
                    <div style="font-size:11px;color:var(--text-muted);margin-top:5px;font-family:'Space Mono',monospace;">
                        Current: <span style="color:var(--accent);">{{ $userTimezone ?? 'UTC' }}</span>
                    </div>
                </div>
                <div style="display:flex;justify-content:flex-end;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Timezone</button>
                </div>
            </form>
        </div>
        @endif

        {{-- ── NOTIFICATIONS ── --}}
        @if($section === 'notifications')
        <div class="card card-p fade-up">
            <div style="font-size:10px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-bottom:6px;"><i class="fas fa-bell" style="margin-right:5px;color:var(--accent);"></i>NOTIFICATION PREFERENCES</div>
            <p style="font-size:12px;color:var(--text-muted);margin:0 0 18px;">These settings are saved to the server — they control which alert types trigger notifications for your account.</p>

            <form method="POST" action="{{ route('settings.notifications') }}" id="notifForm">
                @csrf

                @php $items = [
                    ['key'=>'critical_alerts', 'label'=>'Critical Alerts',       'sub'=>'Immediate notification for critical safety events',  'icon'=>'fa-triangle-exclamation', 'color'=>'var(--danger)'],
                    ['key'=>'warning_alerts',  'label'=>'Warning Alerts',        'sub'=>'Notification for sensor threshold warnings',         'icon'=>'fa-exclamation-circle',   'color'=>'var(--warn)'],
                    ['key'=>'device_status',   'label'=>'Device Online/Offline', 'sub'=>'Alert when a device connects or disconnects',        'icon'=>'fa-plug',                 'color'=>'var(--accent)'],
                    ['key'=>'push_enabled',    'label'=>'Browser Notifications', 'sub'=>'Push notifications in the browser',                  'icon'=>'fa-desktop',              'color'=>'var(--accent)'],
                    ['key'=>'email_enabled',   'label'=>'Email Alerts',          'sub'=>'Send email when critical alerts are triggered',      'icon'=>'fa-envelope',             'color'=>'var(--accent)'],
                ]; @endphp

                <div style="display:flex;flex-direction:column;gap:0;">
                    @foreach($items as $i => $item)
                    @php $checked = $prefs ? (bool)($prefs->{$item['key']}) : ($item['key']==='critical_alerts'||$item['key']==='warning_alerts'||$item['key']==='push_enabled'||$item['key']==='email_enabled'); @endphp
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 0;{{ $i>0?'border-top:1px solid var(--border);':'' }}">
                        <div style="display:flex;align-items:center;gap:13px;">
                            <div style="width:36px;height:36px;border-radius:9px;background:rgba(34,211,238,.07);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas {{ $item['icon'] }}" style="font-size:14px;color:{{ $item['color'] }};"></i>
                            </div>
                            <div>
                                <div style="font-size:13px;font-weight:600;color:#fff;">{{ $item['label'] }}</div>
                                <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">{{ $item['sub'] }}</div>
                            </div>
                        </div>
                        {{-- Hidden checkbox that the form actually submits --}}
                        <input type="hidden" name="{{ $item['key'] }}" value="0">
                        <label style="position:relative;cursor:pointer;flex-shrink:0;" title="{{ $item['label'] }}">
                            <input type="checkbox" name="{{ $item['key'] }}" value="1"
                                   {{ $checked ? 'checked' : '' }}
                                   onchange="updateToggleUI(this)"
                                   style="position:absolute;opacity:0;width:0;height:0;">
                            <div class="toggle-track" data-checked="{{ $checked?'1':'0' }}"
                                 style="width:46px;height:25px;border-radius:99px;position:relative;transition:background .2s;background:{{ $checked?'var(--accent)':'rgba(100,116,139,.2)' }};">
                                <div class="toggle-knob"
                                     style="position:absolute;top:3px;width:19px;height:19px;border-radius:50%;background:#fff;transition:left .2s;left:{{ $checked?'24px':'3px' }};box-shadow:0 1px 4px rgba(0,0,0,.3);"></div>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>

                <div style="border-top:1px solid var(--border);padding-top:16px;margin-top:4px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                    <p style="font-size:11px;color:var(--text-dim);font-family:'Space Mono',monospace;margin:0;">Changes are saved to the database</p>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Preferences</button>
                </div>
            </form>
        </div>
        @endif

        {{-- ── STORAGE ── --}}
        @if($section === 'storage')
        <div class="card card-p fade-up">
            <div style="font-size:10px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-bottom:18px;"><i class="fas fa-database" style="margin-right:5px;color:var(--accent);"></i>STORAGE OVERVIEW</div>
            @php
                $u = auth()->user();
                $readings = \App\Models\SensorReading::where('user_id',$u->id)->count();
                $images   = \App\Models\CameraImage::where('user_id',$u->id)->count();
                $imgSize  = \App\Models\CameraImage::where('user_id',$u->id)->sum('file_size');
                $alerts   = \App\Models\Alert::where('user_id',$u->id)->count();
                $devices  = $u->devices()->count();
            @endphp
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:10px;margin-bottom:20px;">
                @foreach([
                    ['Sensor Readings', number_format($readings), 'var(--accent)',  'fa-chart-line'],
                    ['Camera Images',   number_format($images),   'var(--warn)',    'fa-camera'],
                    ['Alert Records',   number_format($alerts),   'var(--danger)', 'fa-triangle-exclamation'],
                    ['Devices',         $devices,                  'var(--safe)',   'fa-microchip'],
                ] as $s)
                <div class="sensor-box">
                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:8px;">
                        <i class="fas {{ $s[3] }}" style="font-size:13px;color:{{ $s[2] }};"></i>
                        <div class="s-label">{{ $s[0] }}</div>
                    </div>
                    <div style="font-size:24px;font-weight:700;color:{{ $s[2] }};font-family:'Space Mono',monospace;">{{ $s[1] }}</div>
                </div>
                @endforeach
            </div>
            @if($imgSize > 0)
            <div style="background:rgba(34,211,238,.04);border:1px solid var(--border);border-radius:8px;padding:12px 14px;">
                <div style="font-size:12px;color:var(--text-muted);">Total image storage: <span style="color:#fff;font-weight:600;font-family:'Space Mono',monospace;">{{ number_format($imgSize/1024/1024, 2) }} MB</span></div>
            </div>
            @endif
            <div style="background:rgba(251,191,36,.05);border:1px solid rgba(251,191,36,.12);border-radius:8px;padding:11px 14px;font-size:12px;color:var(--warn);margin-top:12px;">
                <i class="fas fa-lightbulb" style="margin-right:5px;"></i>
                Sensor readings are stored indefinitely. Archive old data periodically to keep the database fast.
            </div>
        </div>
        @endif

        {{-- ── ABOUT ── --}}
@if($section === 'about')
<div class="bg-gray-900 border border-gray-800 rounded-2xl shadow-xl p-8 fade-up">

    <!-- Title -->
    <h3 class="text-2xl font-semibold text-white tracking-tight mb-8">
        About HomeGuard
    </h3>

    <div class="space-y-8">

        <!-- Main Info -->
        <div class="bg-gray-800/60 border border-gray-700 rounded-xl p-6">
            <div class="flex items-start gap-4">
                <div class="bg-indigo-600/20 p-3 rounded-lg">
                    <i class="fas fa-home text-2xl text-indigo-400"></i>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-white">
                        HomeGuard IoT Dashboard
                    </h4>
                    <p class="text-gray-400 mt-2 leading-relaxed">
                        Smart Home Safety & Monitoring System integrating ESP32-CAM
                        with a real-time web-based dashboard.
                    </p>

                    <div class="mt-4 text-sm text-gray-500 space-y-1">
                        <p><span class="text-gray-400 font-medium">Version:</span> 1.0.0</p>
                        <p><span class="text-gray-400 font-medium">Last Updated:</span> January 2026</p>
                        <p><span class="text-gray-400 font-medium">Built with:</span> Laravel 11, Tailwind CSS, Chart.js</p>
                        <p><span class="text-gray-400 font-medium">Developed by:</span> Eidref Jake S. Manalansan</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Features -->
        <div>
            <h4 class="text-lg font-semibold text-white mb-5">
                Key Features
            </h4>

            <ul class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <li class="flex items-center gap-3 text-gray-400">
                    <i class="fas fa-check text-indigo-400"></i>
                    Real-time sensor monitoring
                </li>
                <li class="flex items-center gap-3 text-gray-400">
                    <i class="fas fa-check text-indigo-400"></i>
                    Live ESP32-CAM image capture
                </li>
                <li class="flex items-center gap-3 text-gray-400">
                    <i class="fas fa-check text-indigo-400"></i>
                    Instant alert notifications
                </li>
                <li class="flex items-center gap-3 text-gray-400">
                    <i class="fas fa-check text-indigo-400"></i>
                    Historical data & analytics charts
                </li>
                <li class="flex items-center gap-3 text-gray-400">
                    <i class="fas fa-check text-indigo-400"></i>
                    Customizable safety thresholds
                </li>
                <li class="flex items-center gap-3 text-gray-400">
                    <i class="fas fa-check text-indigo-400"></i>
                    Fully responsive dashboard UI
                </li>
            </ul>
        </div>

        <!-- Technology Stack -->
        <div class="bg-gray-800/60 border border-gray-700 rounded-xl p-6">
            <h4 class="text-lg font-semibold text-white mb-6">
                Technology Stack
            </h4>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-6 text-sm text-center">
                <div>
                    <p class="text-white font-medium">Backend</p>
                    <p class="text-gray-500 mt-1">Laravel 11</p>
                </div>
                <div>
                    <p class="text-white font-medium">Frontend</p>
                    <p class="text-gray-500 mt-1">Tailwind CSS</p>
                </div>
                <div>
                    <p class="text-white font-medium">Database</p>
                    <p class="text-gray-500 mt-1">MySQL / SQLite</p>
                </div>
                <div>
                    <p class="text-white font-medium">Charts</p>
                    <p class="text-gray-500 mt-1">Chart.js</p>
                </div>
                <div>
                    <p class="text-white font-medium">IoT Device</p>
                    <p class="text-gray-500 mt-1">ESP32 / ESP32-CAM</p>
                </div>
                <div>
                    <p class="text-white font-medium">Sensors</p>
                    <p class="text-gray-500 mt-1">DHT22, MQ-series, PIR</p>
                </div>
            </div>
        </div>

    </div>
</div>
@endif

    </div>
</div>
@endsection

@section('scripts')
<script>
// Toggle UI sync with checkbox
function updateToggleUI(checkbox) {
    const track = checkbox.closest('label').querySelector('.toggle-track');
    const knob  = track.querySelector('.toggle-knob');
    track.style.background = checkbox.checked ? 'var(--accent)' : 'rgba(100,116,139,.2)';
    knob.style.left = checkbox.checked ? '24px' : '3px';
}

// Responsive sidebar
function adjLayout() {
    const l = document.getElementById('settingsLayout');
    if (l) l.style.gridTemplateColumns = window.innerWidth < 768 ? '1fr' : '200px 1fr';
}
adjLayout(); window.addEventListener('resize', adjLayout);
</script>
@endsection
