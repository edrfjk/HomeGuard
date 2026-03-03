@extends('layouts.app')

@section('title', 'Devices — HomeGuard')
@section('page-title', 'Devices')
@section('page-subtitle', 'Manage your IoT sensors and cameras')

@section('content')
<div style="display:flex;flex-direction:column;gap:20px;">

    {{-- Header row --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;" class="fade-up">
        <div style="font-size:12px;color:var(--text-muted);font-family:'Space Mono',monospace;">
            {{ $devices->count() }} device{{ $devices->count() !== 1 ? 's' : '' }} registered
        </div>
        <a href="/devices/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Register Device
        </a>
    </div>

    @if($devices->isEmpty())
        <div class="card card-p fade-up" style="text-align:center;padding:60px 20px;">
            <i class="fas fa-microchip" style="font-size:48px;display:block;margin-bottom:16px;color:var(--text-dim);"></i>
            <h2 style="font-size:18px;font-weight:700;color:#fff;margin:0 0 8px;">No devices yet</h2>
            <p style="color:var(--text-muted);font-size:14px;margin:0 0 20px;">Register your first ESP32 device to start monitoring</p>
            <a href="/devices/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Register First Device
            </a>
        </div>
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;">
            @foreach($devices as $device)
                @php $reading = $device->latestReading(); @endphp
                <div class="card fade-up" style="animation-delay:{{ $loop->index * 0.07 }}s;padding:20px;transition:border-color .2s,transform .2s;"
                     onmouseenter="this.style.borderColor='rgba(34,211,238,.3)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.borderColor='';this.style.transform=''">

                    {{-- Header --}}
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;">
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:16px;font-weight:700;color:#fff;margin-bottom:4px;">{{ $device->name }}</div>
                            <div style="font-size:11px;color:var(--text-muted);">
                                <i class="fas fa-location-dot" style="margin-right:4px;font-size:10px;"></i>{{ $device->location }}
                            </div>
                        </div>
                        <span class="status-pill {{ $device->status }}">
                            <span class="status-dot {{ $device->status }}"></span>
                            {{ ucfirst($device->status) }}
                        </span>
                    </div>

                    {{-- Device ID --}}
                    <div style="font-family:'Space Mono',monospace;font-size:10px;color:var(--text-dim);background:rgba(255,255,255,.03);border:1px solid var(--border);border-radius:6px;padding:7px 10px;margin-bottom:14px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        {{ $device->device_id }}
                    </div>

                    {{-- Readings --}}
                    @if($reading)
                        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:14px;">
                            <div class="sensor-box">
                                <div class="s-label">Temp</div>
                                <div class="s-val" style="color:var(--warn);">{{ number_format($reading->temperature,1) }}°</div>
                            </div>
                            <div class="sensor-box">
                                <div class="s-label">Humid</div>
                                <div class="s-val" style="color:var(--accent);">{{ number_format($reading->humidity,0) }}%</div>
                            </div>
                            <div class="sensor-box">
                                <div class="s-label">Gas</div>
                                <div class="s-val" style="color:var(--danger);">{{ round($reading->gas_level) }}</div>
                            </div>
                        </div>
                        <div style="font-size:10px;color:var(--text-dim);font-family:'Space Mono',monospace;margin-bottom:14px;">
                            <i class="fas fa-clock" style="margin-right:4px;"></i>{{ $reading->created_at->diffForHumans() }}
                        </div>
                    @else
                        <div style="text-align:center;padding:16px 0;color:var(--text-dim);font-size:12px;margin-bottom:14px;">
                            <i class="fas fa-satellite-dish" style="font-size:24px;display:block;margin-bottom:6px;"></i>
                            Awaiting data from device
                        </div>
                    @endif

                    {{-- Active alerts indicator --}}
                    @php $activeCount = $device->activeAlerts()->count(); @endphp
                    @if($activeCount > 0)
                        <div style="background:rgba(248,113,113,.08);border:1px solid rgba(248,113,113,.2);border-radius:7px;padding:7px 12px;font-size:11px;color:var(--danger);margin-bottom:14px;font-family:'Space Mono',monospace;">
                            <i class="fas fa-bell" style="margin-right:6px;"></i>{{ $activeCount }} active alert{{ $activeCount > 1 ? 's' : '' }}
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div style="display:flex;gap:8px;">
                        <a href="/device/{{ $device->id }}" class="btn btn-primary btn-sm" style="flex:1;justify-content:center;">
                            <i class="fas fa-chart-line"></i> Monitor
                        </a>
                        <a href="/devices/{{ $device->id }}/edit" class="btn btn-ghost btn-sm" style="justify-content:center;" title="Edit">
                            <i class="fas fa-pen"></i>
                        </a>
                        <form method="POST" action="/devices/{{ $device->id }}" onsubmit="return confirm('Delete {{ $device->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ESP32 Integration Guide --}}
        <div class="card card-p fade-up" style="animation-delay:.4s;">
            <h3 style="font-size:15px;font-weight:700;color:#fff;margin:0 0 18px;">
                <i class="fas fa-plug" style="color:var(--accent);margin-right:8px;"></i>ESP32 Integration
            </h3>
            <p style="font-size:13px;color:var(--text-muted);margin:0 0 16px;">Send data from your ESP32 to these endpoints:</p>

            <div style="display:flex;flex-direction:column;gap:12px;">

                {{-- Sensor Data --}}
                <div style="background:rgba(255,255,255,.02);border:1px solid var(--border);border-radius:10px;padding:16px;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                        <span style="background:rgba(34,211,238,.1);color:var(--accent);padding:3px 10px;border-radius:4px;font-size:11px;font-weight:700;font-family:'Space Mono',monospace;">POST</span>
                        <code style="font-family:'Space Mono',monospace;font-size:12px;color:#fff;">/api/sensor-data</code>
                    </div>
                    <pre style="background:var(--bg-deep);border:1px solid var(--border);border-radius:7px;padding:12px;font-size:11px;color:#94a3b8;font-family:'Space Mono',monospace;overflow-x:auto;margin:0;">{
  "device_id": "YOUR_DEVICE_ID",
  "temperature": 28.5,
  "humidity": 62.1,
  "gas_level": 320,
  "signal_strength": -65
}</pre>
                </div>

                {{-- Motion --}}
                <div style="background:rgba(255,255,255,.02);border:1px solid var(--border);border-radius:10px;padding:16px;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                        <span style="background:rgba(34,211,238,.1);color:var(--accent);padding:3px 10px;border-radius:4px;font-size:11px;font-weight:700;font-family:'Space Mono',monospace;">POST</span>
                        <code style="font-family:'Space Mono',monospace;font-size:12px;color:#fff;">/api/motion-detected</code>
                    </div>
                    <pre style="background:var(--bg-deep);border:1px solid var(--border);border-radius:7px;padding:12px;font-size:11px;color:#94a3b8;font-family:'Space Mono',monospace;overflow-x:auto;margin:0;">{
  "device_id": "YOUR_DEVICE_ID"
}</pre>
                </div>

                {{-- Health check --}}
                <div style="background:rgba(255,255,255,.02);border:1px solid var(--border);border-radius:10px;padding:16px;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                        <span style="background:rgba(52,211,153,.1);color:var(--safe);padding:3px 10px;border-radius:4px;font-size:11px;font-weight:700;font-family:'Space Mono',monospace;">GET</span>
                        <code style="font-family:'Space Mono',monospace;font-size:12px;color:#fff;">/api/ping</code>
                        <span style="font-size:11px;color:var(--text-muted);">— Test connectivity</span>
                    </div>
                </div>

            </div>

            <div style="margin-top:16px;padding:12px 14px;background:rgba(251,191,36,.05);border:1px solid rgba(251,191,36,.15);border-radius:8px;font-size:12px;color:var(--warn);">
                <i class="fas fa-lightbulb" style="margin-right:6px;"></i>
                <strong>Tip:</strong> Use <code style="font-family:'Space Mono',monospace;background:rgba(0,0,0,.3);padding:1px 6px;border-radius:4px;">Content-Type: application/json</code> in your ESP32 HTTP request headers.
            </div>
        </div>
    @endif
</div>
@endsection
