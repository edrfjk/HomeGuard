@extends('layouts.app')

@section('title', 'Dashboard — HomeGuard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Real-time home safety overview')

@section('content')
<div style="display:flex;flex-direction:column;gap:24px;">

    {{-- ── Stat Cards ── --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;">
        <div class="stat-card blue fade-up" style="animation-delay:0s">
            <div class="stat-icon blue"><i class="fas fa-microchip"></i></div>
            <div class="stat-label">Total Devices</div>
            <div class="stat-value blue">{{ $totalDevices }}</div>
            <div class="stat-sub"><i class="fas fa-check-circle"></i> Registered</div>
        </div>
        <div class="stat-card green fade-up" style="animation-delay:0.07s">
            <div class="stat-icon green"><i class="fas fa-wifi"></i></div>
            <div class="stat-label">Online Now</div>
            <div class="stat-value green">{{ $onlineDevices }}/{{ $totalDevices }}</div>
            <div class="stat-sub"><i class="fas fa-signal"></i> Live connections</div>
        </div>
        <div class="stat-card orange fade-up" style="animation-delay:0.14s">
            <div class="stat-icon orange"><i class="fas fa-bell"></i></div>
            <div class="stat-label">Active Alerts</div>
            <div class="stat-value orange">{{ $stats['active'] }}</div>
            <div class="stat-sub">
                @if($stats['active'] > 0)
                    <i class="fas fa-circle" style="color:var(--warn);font-size:7px;"></i> Requires attention
                @else
                    <i class="fas fa-check-circle"></i> All clear
                @endif
            </div>
        </div>
        <div class="stat-card red fade-up" style="animation-delay:0.21s">
            <div class="stat-icon red"><i class="fas fa-triangle-exclamation"></i></div>
            <div class="stat-label">Critical Alerts</div>
            <div class="stat-value red">{{ $stats['critical'] }}</div>
            <div class="stat-sub">
                @if($stats['critical'] > 0)
                    <i class="fas fa-circle" style="color:var(--danger);font-size:7px;animation:pulse-safe 1s infinite;"></i> Immediate action needed
                @else
                    <i class="fas fa-shield-halved"></i> All safe
                @endif
            </div>
        </div>
    </div>

    {{-- ── Devices Grid ── --}}
    <div class="card card-p fade-up" style="animation-delay:0.28s">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
            <div>
                <h2 style="font-size:16px;font-weight:700;color:#fff;margin:0;">Your Devices</h2>
                <p style="font-size:12px;color:var(--text-muted);margin:4px 0 0;font-family:'Space Mono',monospace;">Live sensor readings</p>
            </div>
            <a href="/devices/create" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Device
            </a>
        </div>

        @if($devices->isEmpty())
            <div style="text-align:center;padding:40px 20px;color:var(--text-muted);">
                <i class="fas fa-microchip" style="font-size:40px;margin-bottom:12px;display:block;opacity:.3;"></i>
                <p style="font-size:14px;margin:0 0 12px;">No devices registered yet</p>
                <a href="/devices/create" class="btn btn-primary btn-sm">Register your first device</a>
            </div>
        @else
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
                @foreach($devices as $device)
                    @php $reading = $latestReadings[$device->id] ?? null; @endphp
                    <div class="card" style="padding:18px;transition:border-color .2s,transform .2s;cursor:pointer;" onclick="location.href='/device/{{ $device->id }}'"
                         onmouseenter="this.style.borderColor='rgba(34,211,238,.3)';this.style.transform='translateY(-2px)'"
                         onmouseleave="this.style.borderColor='';this.style.transform=''">
                        {{-- Header --}}
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;">
                            <div>
                                <div style="font-size:15px;font-weight:700;color:#fff;">{{ $device->name }}</div>
                                <div style="font-size:11px;color:var(--text-muted);margin-top:3px;">
                                    <i class="fas fa-location-dot" style="font-size:10px;margin-right:4px;"></i>{{ $device->location }}
                                </div>
                            </div>
                            <span class="status-pill {{ $device->status }}">
                                <span class="status-dot {{ $device->status }}"></span>
                                {{ ucfirst($device->status) }}
                            </span>
                        </div>

                        {{-- Sensor data --}}
                        @if($reading)
                            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:14px;">
                                <div class="sensor-box">
                                    <div class="s-label">Temp</div>
                                    <div class="s-val" style="color:var(--warn);">{{ number_format($reading->temperature, 1) }}°</div>
                                </div>
                                <div class="sensor-box">
                                    <div class="s-label">Humid</div>
                                    <div class="s-val" style="color:var(--accent);">{{ number_format($reading->humidity, 0) }}%</div>
                                </div>
                                <div class="sensor-box">
                                    <div class="s-label">Gas</div>
                                    <div class="s-val" style="color:var(--danger);">{{ round($reading->gas_level) }}</div>
                                </div>
                            </div>
                            <div style="font-size:10px;color:var(--text-muted);margin-bottom:14px;font-family:'Space Mono',monospace;">
                                <i class="fas fa-clock" style="margin-right:4px;"></i>Updated {{ $reading->created_at->diffForHumans() }}
                            </div>
                        @else
                            <div style="padding:14px 0;text-align:center;color:var(--text-dim);font-size:12px;margin-bottom:14px;">
                                <i class="fas fa-satellite-dish" style="font-size:22px;display:block;margin-bottom:6px;"></i>
                                Awaiting first reading
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div style="display:flex;gap:8px;">
                            <a href="/device/{{ $device->id }}" class="btn btn-ghost btn-sm" style="flex:1;justify-content:center;">
                                <i class="fas fa-chart-line"></i> Details
                            </a>
                            <a href="/devices/{{ $device->id }}/edit" class="btn btn-ghost btn-sm" style="justify-content:center;">
                                <i class="fas fa-pen"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ── Recent Alerts ── --}}
    <div class="card card-p fade-up" style="animation-delay:0.35s">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:8px;">
            <div>
                <h2 style="font-size:16px;font-weight:700;color:#fff;margin:0;">Recent Alerts</h2>
                <p style="font-size:12px;color:var(--text-muted);margin:4px 0 0;font-family:'Space Mono',monospace;">Active incidents</p>
            </div>
            <a href="/alerts" class="btn btn-ghost btn-sm">View all <i class="fas fa-arrow-right"></i></a>
        </div>

        @php
            $recentAlerts = auth()->user()->alerts()->where('status', 'active')->with('device')->latest()->take(8)->get();
        @endphp

        @forelse($recentAlerts as $alert)
            <a href="{{ route('alerts.show', $alert) }}" class="alert-item {{ $alert->severity }}">
                <span style="font-size:18px;flex-shrink:0;">
                    {{ $alert->severity === 'critical' ? '🚨' : ($alert->severity === 'warning' ? '⚠️' : 'ℹ️') }}
                </span>
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;">
                        <span style="font-size:13px;font-weight:600;color:#fff;">{{ $alert->device->name ?? 'Unknown' }}</span>
                        <span class="alert-pill {{ $alert->severity }}">{{ strtoupper($alert->severity) }}</span>
                    </div>
                    <div style="font-size:12px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $alert->message }}</div>
                    <div style="font-size:11px;color:var(--text-dim);margin-top:4px;font-family:'Space Mono',monospace;">
                        <i class="fas fa-clock" style="margin-right:4px;"></i>{{ $alert->created_at->diffForHumans() }}
                    </div>
                </div>
                <i class="fas fa-chevron-right" style="color:var(--text-dim);font-size:11px;"></i>
            </a>
        @empty
            <div style="text-align:center;padding:30px;color:var(--text-muted);">
                <i class="fas fa-shield-halved" style="font-size:32px;display:block;margin-bottom:10px;color:var(--safe);"></i>
                <p style="font-size:13px;margin:0;">No active alerts — all systems safe</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
