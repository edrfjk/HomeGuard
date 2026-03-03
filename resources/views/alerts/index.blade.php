@extends('layouts.app')

@section('title', 'Alerts — HomeGuard')
@section('page-title', 'Alerts')
@section('page-subtitle', 'All system events and safety warnings')

@section('content')
<div style="display:flex;flex-direction:column;gap:18px;">

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;" class="fade-up">
        @php $cards = [
            ['label'=>'TOTAL',    'val'=>$stats['total'],    'color'=>'var(--text-muted)', 'icon'=>'fa-list'],
            ['label'=>'ACTIVE',   'val'=>$stats['active'],   'color'=>'var(--warn)',       'icon'=>'fa-clock'],
            ['label'=>'CRITICAL', 'val'=>$stats['critical'], 'color'=>'var(--danger)',     'icon'=>'fa-triangle-exclamation'],
            ['label'=>'RESOLVED', 'val'=>$stats['resolved'], 'color'=>'var(--safe)',       'icon'=>'fa-check-circle'],
        ]; @endphp
        @foreach($cards as $c)
        <div class="card card-p" style="border-top:2px solid {{ $c['color'] }};">
            <div style="font-size:10px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-bottom:8px;">
                <i class="fas {{ $c['icon'] }}" style="margin-right:5px;color:{{ $c['color'] }};"></i>{{ $c['label'] }}
            </div>
            <div style="font-size:30px;font-weight:700;color:{{ $c['color'] }};font-family:'Space Mono',monospace;">{{ $c['val'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="card card-p fade-up" style="animation-delay:.05s;">
        <form method="GET" id="filterForm">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:10px;margin-bottom:12px;">
                <div>
                    <div style="font-size:10px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-bottom:4px;">DEVICE</div>
                    <select name="device" class="form-control" style="padding:7px 10px;font-size:12px;" onchange="this.form.submit()">
                        <option value="all" {{ $deviceFilter==='all'?'selected':'' }}>All devices</option>
                        @foreach(auth()->user()->devices as $d)
                            <option value="{{ $d->id }}" {{ $deviceFilter==(string)$d->id?'selected':'' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <div style="font-size:10px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-bottom:4px;">STATUS</div>
                    <select name="status" class="form-control" style="padding:7px 10px;font-size:12px;" onchange="this.form.submit()">
                        <option value="all"      {{ $statusFilter==='all'?'selected':'' }}>All</option>
                        <option value="active"   {{ $statusFilter==='active'?'selected':'' }}>Active</option>
                        <option value="resolved" {{ $statusFilter==='resolved'?'selected':'' }}>Resolved</option>
                    </select>
                </div>
                <div>
                    <div style="font-size:10px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-bottom:4px;">SEVERITY</div>
                    <select name="severity" class="form-control" style="padding:7px 10px;font-size:12px;" onchange="this.form.submit()">
                        <option value="all"      {{ $severityFilter==='all'?'selected':'' }}>All</option>
                        <option value="critical" {{ $severityFilter==='critical'?'selected':'' }}>🚨 Critical</option>
                        <option value="warning"  {{ $severityFilter==='warning'?'selected':'' }}>⚠️ Warning</option>
                    </select>
                </div>
                <div>
                    <div style="font-size:10px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-bottom:4px;">DATE</div>
                    <select name="date" class="form-control" style="padding:7px 10px;font-size:12px;" onchange="this.form.submit()">
                        <option value="all"   {{ $dateFilter==='all'?'selected':'' }}>All time</option>
                        <option value="today" {{ $dateFilter==='today'?'selected':'' }}>Today</option>
                        <option value="week"  {{ $dateFilter==='week'?'selected':'' }}>This week</option>
                        <option value="month" {{ $dateFilter==='month'?'selected':'' }}>This month</option>
                    </select>
                </div>
                <div>
                    <div style="font-size:10px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-bottom:4px;">TYPE</div>
                    <select name="type" class="form-control" style="padding:7px 10px;font-size:12px;" onchange="this.form.submit()">
                        <option value="all" {{ $typeFilter==='all'?'selected':'' }}>All types</option>
                        <option value="motion_detected"      {{ $typeFilter==='motion_detected'?'selected':'' }}>🏃 Motion</option>
                        <option value="temperature_critical" {{ $typeFilter==='temperature_critical'?'selected':'' }}>🌡️ Temp Critical</option>
                        <option value="temperature_warning"  {{ $typeFilter==='temperature_warning'?'selected':'' }}>🌡️ Temp Warning</option>
                        <option value="humidity_critical"    {{ $typeFilter==='humidity_critical'?'selected':'' }}>💧 Humid Critical</option>
                        <option value="humidity_warning"     {{ $typeFilter==='humidity_warning'?'selected':'' }}>💧 Humid Warning</option>
                        <option value="gas_critical"         {{ $typeFilter==='gas_critical'?'selected':'' }}>☠️ Gas Critical</option>
                        <option value="gas_warning"          {{ $typeFilter==='gas_warning'?'selected':'' }}>☠️ Gas Warning</option>
                    </select>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:11px;color:var(--text-dim);font-family:'Space Mono',monospace;">
                    {{ $alerts->total() }} result{{ $alerts->total()===1?'':'s' }}
                </span>
                @if($deviceFilter!=='all'||$statusFilter!=='all'||$severityFilter!=='all'||$dateFilter!=='all'||$typeFilter!=='all')
                    <a href="{{ route('alerts.index') }}" class="btn btn-ghost btn-sm" style="font-size:11px;padding:4px 10px;">
                        <i class="fas fa-xmark"></i> Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Alert rows --}}
    <div style="display:flex;flex-direction:column;gap:8px;" class="fade-up" style="animation-delay:.1s;">
        @forelse($alerts as $i => $alert)
        @php
            $sc = $alert->severity==='critical'?'var(--danger)':'var(--warn)';
            $delay = min($i*0.04, 0.3);
        @endphp
        <div style="background:var(--bg-card);border:1px solid var(--border);border-left:3px solid {{ $sc }};border-radius:10px;padding:13px 15px;display:flex;align-items:center;gap:13px;transition:all .15s;animation:fadeUp .4s ease-out {{ $delay }}s both;"
             onmouseenter="this.style.background='rgba(255,255,255,.025)';this.style.borderColor='rgba(34,211,238,.15)'"
             onmouseleave="this.style.background='var(--bg-card)';this.style.borderColor='var(--border)';this.style.borderLeftColor='{{ $sc }}'">

            <span style="font-size:18px;flex-shrink:0;">{{ $alert->severity==='critical'?'🚨':($alert->severity==='warning'?'⚠️':'ℹ️') }}</span>

            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:7px;flex-wrap:wrap;margin-bottom:4px;">
                    <span style="font-size:13px;font-weight:700;color:#fff;">{{ $alert->device->name }}</span>
                    <span style="font-size:10px;color:var(--text-dim);font-family:'Space Mono',monospace;">{{ str_replace('_',' ',ucfirst($alert->type)) }}</span>
                    {{-- severity pill --}}
                    <span style="background:{{ $alert->severity==='critical'?'rgba(248,113,113,.1)':'rgba(251,191,36,.1)' }};border:1px solid {{ $alert->severity==='critical'?'rgba(248,113,113,.25)':'rgba(251,191,36,.25)' }};color:{{ $sc }};padding:1px 7px;border-radius:99px;font-size:9px;font-weight:700;font-family:'Space Mono',monospace;">
                        {{ strtoupper($alert->severity) }}
                    </span>
                    {{-- status pill --}}
                    @if($alert->status==='resolved')
                        <span style="background:rgba(52,211,153,.1);border:1px solid rgba(52,211,153,.2);color:var(--safe);padding:1px 7px;border-radius:99px;font-size:9px;font-weight:700;font-family:'Space Mono',monospace;">RESOLVED</span>
                    @elseif($alert->status==='acknowledged')
                        <span style="background:rgba(96,165,250,.1);border:1px solid rgba(96,165,250,.2);color:#60a5fa;padding:1px 7px;border-radius:99px;font-size:9px;font-weight:700;font-family:'Space Mono',monospace;">ACK'D</span>
                    @else
                        <span style="background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.2);color:var(--warn);padding:1px 7px;border-radius:99px;font-size:9px;font-weight:700;font-family:'Space Mono',monospace;">ACTIVE</span>
                    @endif
                </div>
                <p style="font-size:12px;color:var(--text-muted);margin:0 0 4px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:480px;">{{ $alert->message }}</p>
                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <span style="font-size:10px;color:var(--text-dim);font-family:'Space Mono',monospace;"><i class="fas fa-clock" style="margin-right:3px;"></i>{{ $alert->created_at->diffForHumans() }}</span>
                    <span style="font-size:10px;color:var(--text-dim);font-family:'Space Mono',monospace;"><i class="fas fa-location-dot" style="margin-right:3px;"></i>{{ $alert->device->location }}</span>
                    @if($alert->reading_value)<span style="font-size:10px;color:var(--text-dim);font-family:'Space Mono',monospace;"><i class="fas fa-gauge" style="margin-right:3px;"></i>{{ $alert->reading_value }}</span>@endif
                </div>
            </div>

            <div style="display:flex;gap:6px;flex-shrink:0;align-items:center;">
                @if($alert->status==='active')
                <form action="{{ route('alerts.resolve', $alert) }}" method="POST">
                    @csrf
                    <button type="submit" title="Mark resolved"
                            style="width:30px;height:30px;border-radius:7px;border:1px solid rgba(52,211,153,.2);background:rgba(52,211,153,.08);color:var(--safe);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .15s;"
                            onmouseenter="this.style.background='rgba(52,211,153,.22)'" onmouseleave="this.style.background='rgba(52,211,153,.08)'">
                        <i class="fas fa-check" style="font-size:11px;"></i>
                    </button>
                </form>
                @endif
                <a href="{{ route('alerts.show', $alert) }}"
                   style="background:rgba(34,211,238,.07);border:1px solid rgba(34,211,238,.14);color:var(--accent);padding:5px 11px;border-radius:7px;text-decoration:none;font-size:11px;font-weight:600;white-space:nowrap;transition:background .15s;"
                   onmouseenter="this.style.background='rgba(34,211,238,.18)'" onmouseleave="this.style.background='rgba(34,211,238,.07)'">
                    Details <i class="fas fa-chevron-right" style="font-size:9px;margin-left:3px;"></i>
                </a>
            </div>
        </div>
        @empty
        <div class="card card-p" style="text-align:center;padding:60px 20px;">
            <i class="fas fa-shield-check" style="font-size:44px;color:var(--safe);display:block;margin-bottom:14px;opacity:.5;"></i>
            <p style="font-size:15px;font-weight:700;color:#fff;margin:0 0 8px;">No alerts found</p>
            <p style="font-size:13px;color:var(--text-muted);margin:0 0 16px;">{{ request()->hasAny(['status','severity','date','type','device']) ? 'Try adjusting your filters' : 'Your system is running clean' }}</p>
            @if(request()->hasAny(['status','severity','date','type','device']))
                <a href="{{ route('alerts.index') }}" class="btn btn-ghost btn-sm">Clear filters</a>
            @endif
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($alerts->hasPages())
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;" class="fade-up">
        <span style="font-size:11px;color:var(--text-muted);font-family:'Space Mono',monospace;">{{ $alerts->firstItem() }}–{{ $alerts->lastItem() }} of {{ $alerts->total() }}</span>
        <div style="display:flex;gap:5px;align-items:center;">
            @if($alerts->onFirstPage())
                <span class="btn btn-ghost btn-sm" style="opacity:.35;cursor:default;"><i class="fas fa-chevron-left"></i></span>
            @else
                <a href="{{ $alerts->previousPageUrl() }}" class="btn btn-ghost btn-sm"><i class="fas fa-chevron-left"></i></a>
            @endif
            @for($p=max(1,$alerts->currentPage()-2);$p<=min($alerts->lastPage(),$alerts->currentPage()+2);$p++)
                @if($p==$alerts->currentPage())
                    <span class="btn btn-sm" style="background:var(--accent);color:#000;cursor:default;min-width:32px;justify-content:center;">{{ $p }}</span>
                @else
                    <a href="{{ $alerts->url($p) }}" class="btn btn-ghost btn-sm" style="min-width:32px;justify-content:center;">{{ $p }}</a>
                @endif
            @endfor
            @if($alerts->hasMorePages())
                <a href="{{ $alerts->nextPageUrl() }}" class="btn btn-ghost btn-sm"><i class="fas fa-chevron-right"></i></a>
            @else
                <span class="btn btn-ghost btn-sm" style="opacity:.35;cursor:default;"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif

</div>
@endsection
