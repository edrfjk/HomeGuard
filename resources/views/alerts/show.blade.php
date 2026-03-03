@extends('layouts.app')

@section('title', 'Alert Detail — HomeGuard')
@section('page-title', 'Alert Detail')
@section('page-subtitle', $alert->device->name . ' · ' . $alert->device->location)

@section('content')
@php
    $sc  = $alert->severity==='critical' ? 'var(--danger)' : ($alert->severity==='warning' ? 'var(--warn)' : 'var(--accent)');
    $bg  = $alert->severity==='critical' ? 'rgba(248,113,113,.05)' : ($alert->severity==='warning' ? 'rgba(251,191,36,.05)' : 'rgba(34,211,238,.05)');
@endphp

<div style="display:flex;flex-direction:column;gap:16px;max-width:860px;">

    <a href="{{ route('alerts.index') }}" class="btn btn-ghost btn-sm" style="align-self:flex-start;">
        <i class="fas fa-arrow-left"></i> All Alerts
    </a>

    {{-- Banner --}}
    <div class="card fade-up" style="border-left:4px solid {{ $sc }};background:{{ $bg }};padding:20px 22px;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:14px;flex-wrap:wrap;">
            <div style="display:flex;align-items:flex-start;gap:14px;">
                <span style="font-size:38px;line-height:1;">{{ $alert->severity==='critical'?'🚨':($alert->severity==='warning'?'⚠️':'ℹ️') }}</span>
                <div>
                    <h2 style="font-size:20px;font-weight:700;color:#fff;margin:0 0 7px;">{{ str_replace('_',' ',ucfirst($alert->type)) }}</h2>
                    <div style="display:flex;gap:7px;flex-wrap:wrap;align-items:center;">
                        <span style="background:{{ $sc }};color:{{ $alert->severity==='warning'?'#000':'#fff' }};padding:3px 10px;border-radius:6px;font-size:10px;font-weight:700;font-family:'Space Mono',monospace;">{{ strtoupper($alert->severity) }}</span>
                        @if($alert->status==='resolved')
                            <span style="background:rgba(52,211,153,.12);border:1px solid rgba(52,211,153,.25);color:var(--safe);padding:3px 10px;border-radius:6px;font-size:10px;font-weight:700;font-family:'Space Mono',monospace;">✓ RESOLVED</span>
                        @elseif($alert->status==='acknowledged')
                            <span style="background:rgba(96,165,250,.12);border:1px solid rgba(96,165,250,.25);color:#60a5fa;padding:3px 10px;border-radius:6px;font-size:10px;font-weight:700;font-family:'Space Mono',monospace;">ACKNOWLEDGED</span>
                        @else
                            <span style="background:rgba(251,191,36,.12);border:1px solid rgba(251,191,36,.25);color:var(--warn);padding:3px 10px;border-radius:6px;font-size:10px;font-weight:700;font-family:'Space Mono',monospace;">● ACTIVE</span>
                        @endif
                        <span style="font-size:11px;color:var(--text-muted);font-family:'Space Mono',monospace;">{{ $alert->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
            @if($alert->status==='active')
            <div style="display:flex;gap:8px;flex-shrink:0;">
                <form action="{{ route('alerts.acknowledge', $alert) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-sm"><i class="fas fa-eye"></i> Acknowledge</button>
                </form>
                <form action="{{ route('alerts.resolve', $alert) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check"></i> Resolve</button>
                </form>
            </div>
            @endif
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 280px;gap:16px;" id="detailGrid">

        {{-- Left col --}}
        <div style="display:flex;flex-direction:column;gap:14px;">

            {{-- Message --}}
            <div class="card card-p fade-up" style="animation-delay:.05s;">
                <div style="font-size:10px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-bottom:10px;"><i class="fas fa-message" style="margin-right:5px;color:var(--accent);"></i>MESSAGE</div>
                <p style="font-size:14px;color:#fff;line-height:1.65;margin:0;padding-left:12px;border-left:2px solid {{ $sc }};">{{ $alert->message }}</p>
            </div>

            {{-- Readings --}}
            @if($alert->reading_value || $alert->threshold_value)
            <div class="card card-p fade-up" style="animation-delay:.08s;">
                <div style="font-size:10px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-bottom:12px;"><i class="fas fa-chart-bar" style="margin-right:5px;color:var(--accent);"></i>TRIGGER VALUES</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    @if($alert->reading_value)
                    <div class="sensor-box">
                        <div class="s-label">Actual Value</div>
                        <div class="s-val" style="color:{{ $sc }};font-size:22px;">{{ $alert->reading_value }}</div>
                    </div>
                    @endif
                    @if($alert->threshold_value)
                    <div class="sensor-box">
                        <div class="s-label">Threshold</div>
                        <div class="s-val" style="color:var(--text-muted);font-size:22px;">{{ $alert->threshold_value }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Camera capture --}}
            @if($alert->cameraImage)
            <div class="card card-p fade-up" style="animation-delay:.11s;">
                <div style="font-size:10px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-bottom:12px;"><i class="fas fa-camera" style="margin-right:5px;color:var(--accent);"></i>MOTION CAPTURE</div>
                <div style="position:relative;aspect-ratio:16/9;border-radius:9px;overflow:hidden;background:#000;cursor:pointer;"
                     onclick="document.getElementById('imgModal').style.display='flex'">
                    <img src="{{ asset('storage/'.$alert->cameraImage->image_path) }}"
                         style="width:100%;height:100%;object-fit:cover;transition:transform .35s;"
                         onmouseenter="this.style.transform='scale(1.04)'" onmouseleave="this.style.transform=''">
                    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0);transition:background .2s;pointer-events:none;" id="imgHover">
                        <i class="fas fa-expand" style="font-size:24px;color:#fff;opacity:0;transition:opacity .2s;" id="imgHoverIcon"></i>
                    </div>
                </div>
                <a href="{{ route('camera.view', $alert->cameraImage) }}" class="btn btn-ghost btn-sm" style="margin-top:10px;display:inline-flex;">
                    <i class="fas fa-external-link-alt"></i> Open in Gallery
                </a>
            </div>
            @endif

            {{-- Details grid --}}
            <div class="card card-p fade-up" style="animation-delay:.14s;">
                <div style="font-size:10px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-bottom:12px;"><i class="fas fa-circle-info" style="margin-right:5px;color:var(--accent);"></i>DETAILS</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    @foreach([
                        ['Alert ID',    '#'.$alert->id],
                        ['Type',        str_replace('_',' ',ucfirst($alert->type))],
                        ['Device',      $alert->device->name],
                        ['Location',    $alert->device->location],
                        ['Triggered',   $alert->created_at->format('M d, Y H:i:s')],
                        ['Status',      ucfirst($alert->status)],
                    ] as $row)
                    <div class="sensor-box" style="padding:9px 12px;">
                        <div class="s-label">{{ $row[0] }}</div>
                        <div style="font-size:12px;font-weight:600;color:#fff;margin-top:3px;font-family:'Space Mono',monospace;">{{ $row[1] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right sidebar --}}
        <div style="display:flex;flex-direction:column;gap:14px;">

            {{-- Timeline --}}
            <div class="card card-p fade-up" style="animation-delay:.06s;">
                <div style="font-size:10px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-bottom:14px;"><i class="fas fa-timeline" style="margin-right:5px;color:var(--accent);"></i>TIMELINE</div>
                <div style="display:flex;flex-direction:column;gap:0;">
                    <div style="display:flex;gap:10px;padding-bottom:14px;">
                        <div style="display:flex;flex-direction:column;align-items:center;">
                            <div style="width:9px;height:9px;border-radius:50%;background:var(--accent);flex-shrink:0;"></div>
                            <div style="width:1px;flex:1;background:var(--border);margin:3px 0;"></div>
                        </div>
                        <div>
                            <div style="font-size:12px;font-weight:600;color:#fff;">Alert triggered</div>
                            <div style="font-size:10px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-top:2px;">{{ $alert->created_at->format('M d, Y H:i:s') }}</div>
                        </div>
                    </div>
                    @if($alert->status==='resolved')
                    <div style="display:flex;gap:10px;">
                        <div style="width:9px;height:9px;border-radius:50%;background:var(--safe);flex-shrink:0;margin-top:2px;"></div>
                        <div>
                            <div style="font-size:12px;font-weight:600;color:var(--safe);">Resolved</div>
                            <div style="font-size:10px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-top:2px;">{{ $alert->resolved_at?->format('M d, Y H:i:s') ?? 'Manually resolved' }}</div>
                        </div>
                    </div>
                    @else
                    <div style="display:flex;gap:10px;opacity:.4;">
                        <div style="width:9px;height:9px;border-radius:50%;border:1px dashed var(--text-muted);flex-shrink:0;margin-top:2px;"></div>
                        <div style="font-size:12px;color:var(--text-muted);">Pending resolution</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Device --}}
            <div class="card card-p fade-up" style="animation-delay:.09s;">
                <div style="font-size:10px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-bottom:12px;"><i class="fas fa-microchip" style="margin-right:5px;color:var(--accent);"></i>DEVICE</div>
                <div style="display:flex;flex-direction:column;gap:9px;">
                    <div>
                        <div style="font-size:10px;color:var(--text-dim);font-family:'Space Mono',monospace;margin-bottom:2px;">NAME</div>
                        <div style="font-size:13px;font-weight:700;color:#fff;">{{ $alert->device->name }}</div>
                    </div>
                    <div>
                        <div style="font-size:10px;color:var(--text-dim);font-family:'Space Mono',monospace;margin-bottom:2px;">LOCATION</div>
                        <div style="font-size:12px;color:var(--text-muted);">{{ $alert->device->location }}</div>
                    </div>
                    <div>
                        <div style="font-size:10px;color:var(--text-dim);font-family:'Space Mono',monospace;margin-bottom:4px;">STATUS</div>
                        <span class="status-pill {{ $alert->device->status }}">
                            <span class="status-dot {{ $alert->device->status }}"></span>{{ ucfirst($alert->device->status) }}
                        </span>
                    </div>
                </div>
                <a href="/device/{{ $alert->device->id }}" class="btn btn-primary btn-sm" style="margin-top:14px;display:flex;justify-content:center;font-size:11px;">
                    <i class="fas fa-gauge-high"></i> Monitor Device
                </a>
            </div>

            {{-- Nav prev/next alert --}}
            @php
                $prev = \App\Models\Alert::where('user_id', auth()->id())->where('id','<',$alert->id)->latest()->first();
                $next = \App\Models\Alert::where('user_id', auth()->id())->where('id','>',$alert->id)->oldest()->first();
            @endphp
            @if($prev || $next)
            <div class="card card-p fade-up" style="animation-delay:.12s;">
                <div style="font-size:10px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-bottom:10px;"><i class="fas fa-arrows-left-right" style="margin-right:5px;"></i>NAVIGATE</div>
                <div style="display:flex;gap:7px;">
                    @if($prev)
                    <a href="{{ route('alerts.show',$prev) }}" class="btn btn-ghost btn-sm" style="flex:1;justify-content:center;font-size:11px;"><i class="fas fa-chevron-left"></i> Older</a>
                    @endif
                    @if($next)
                    <a href="{{ route('alerts.show',$next) }}" class="btn btn-ghost btn-sm" style="flex:1;justify-content:center;font-size:11px;">Newer <i class="fas fa-chevron-right"></i></a>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Image modal --}}
@if($alert->cameraImage)
<div id="imgModal" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.95);align-items:center;justify-content:center;" onclick="this.style.display='none'">
    <img src="{{ asset('storage/'.$alert->cameraImage->image_path) }}" style="max-width:92vw;max-height:88vh;border-radius:10px;object-fit:contain;">
    <button onclick="document.getElementById('imgModal').style.display='none'" style="position:absolute;top:16px;right:16px;background:rgba(255,255,255,.1);border:none;color:#fff;width:36px;height:36px;border-radius:50%;cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center;">✕</button>
</div>
@endif
@endsection

@section('scripts')
<script>
// Hover effect on camera preview
const imgHover = document.getElementById('imgHover');
const imgIcon  = document.getElementById('imgHoverIcon');
if (imgHover) {
    imgHover.parentElement.addEventListener('mouseenter', () => { imgHover.style.background='rgba(0,0,0,.4)'; imgIcon.style.opacity='1'; });
    imgHover.parentElement.addEventListener('mouseleave', () => { imgHover.style.background='rgba(0,0,0,0)'; imgIcon.style.opacity='0'; });
}
document.addEventListener('keydown', e => {
    if (e.key==='Escape') { const m=document.getElementById('imgModal'); if(m) m.style.display='none'; }
});
// Responsive
function adj() {
    const g=document.getElementById('detailGrid');
    if(g) g.style.gridTemplateColumns=window.innerWidth<768?'1fr':'1fr 280px';
}
adj(); window.addEventListener('resize',adj);
</script>
@endsection
