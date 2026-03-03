@extends('layouts.app')

@section('title', 'Register Device — HomeGuard')
@section('page-title', 'Register Device')
@section('page-subtitle', 'Connect a new ESP32 to HomeGuard')

@section('content')
<div style="max-width:600px;">
    <a href="/devices" class="btn btn-ghost btn-sm" style="margin-bottom:20px;display:inline-flex;">
        <i class="fas fa-arrow-left"></i> Back
    </a>

    <div class="card card-p fade-up">
        <h2 style="font-size:16px;font-weight:700;color:#fff;margin:0 0 20px;">
            <i class="fas fa-microchip" style="color:var(--accent);margin-right:8px;"></i>New Device
        </h2>

        <form method="POST" action="/devices">
            @csrf

            <div class="form-group">
                <label class="form-label">Device Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                       placeholder="e.g. Living Room Sensor" required>
                <div style="font-size:11px;color:var(--text-muted);margin-top:5px;font-family:'Space Mono',monospace;">A friendly display name</div>
            </div>

            <div class="form-group">
                <label class="form-label">Device ID <span style="color:var(--accent);">(ESP32 Identifier)</span></label>
                <input type="text" name="device_id" class="form-control mono" value="{{ old('device_id') }}"
                       placeholder="e.g. AA:BB:CC:DD:EE:FF" required style="font-family:'Space Mono',monospace;letter-spacing:0.05em;">
                <div style="font-size:11px;color:var(--text-muted);margin-top:5px;font-family:'Space Mono',monospace;">
                    MAC address or unique ID — must match what your ESP32 sends in the <code style="color:var(--accent);">device_id</code> field
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Location</label>
                <input type="text" name="location" class="form-control" value="{{ old('location') }}"
                       placeholder="e.g. Kitchen, Bedroom, Garage" required>
            </div>

            <div class="form-group">
                <label class="form-label">Description <span style="color:var(--text-dim);">(optional)</span></label>
                <textarea name="description" class="form-control" rows="2" placeholder="Any notes about this device...">{{ old('description') }}</textarea>
            </div>

            <div style="border-top:1px solid var(--border);padding-top:18px;margin-top:8px;display:flex;gap:10px;justify-content:flex-end;">
                <a href="/devices" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Register Device
                </button>
            </div>
        </form>
    </div>

    {{-- Help: How to find device ID --}}
    <div class="card card-p fade-up" style="animation-delay:.1s;margin-top:16px;">
        <h3 style="font-size:13px;font-weight:700;color:#fff;margin:0 0 14px;">
            <i class="fas fa-circle-question" style="color:var(--warn);margin-right:8px;"></i>How to get your ESP32 Device ID
        </h3>
        <p style="font-size:12px;color:var(--text-muted);margin:0 0 12px;">Flash this snippet to your ESP32 and read the Serial Monitor:</p>
        <pre style="background:var(--bg-deep);border:1px solid var(--border);border-radius:8px;padding:14px;font-size:11px;color:#94a3b8;font-family:'Space Mono',monospace;overflow-x:auto;margin:0;line-height:1.7;">#include &lt;WiFi.h&gt;

void setup() {
  Serial.begin(115200);
  // MAC address is a unique hardware ID
  Serial.print("Device ID: ");
  Serial.println(WiFi.macAddress());
}

void loop() {}</pre>
        <div style="margin-top:12px;font-size:11px;color:var(--text-muted);font-family:'Space Mono',monospace;">
            Output example: <span style="color:var(--accent);">Device ID: AA:BB:CC:DD:EE:FF</span>
        </div>
    </div>
</div>
@endsection
