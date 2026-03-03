@extends('layouts.app')

@section('title', 'Motion Capture — HomeGuard')
@section('page-title', 'Capture Detail')
@section('page-subtitle', $image->device->name . ' · ' . $image->created_at->format('M d, Y'))

@section('content')
@php
    $linkedAlert = $image->alert;
    $severity    = $linkedAlert?->severity ?? 'warning';
    $sevColor    = $severity === 'critical' ? 'var(--danger)' : 'var(--warn)';
@endphp

<div style="display:flex;flex-direction:column;gap:18px;max-width:900px;">

    {{-- Nav --}}
    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;" class="fade-up">
        <a href="{{ route('camera.gallery', $image->device_id) }}" class="btn btn-ghost btn-sm">
            <i class="fas fa-arrow-left"></i> Gallery
        </a>
        <span style="color:var(--text-dim);">›</span>
        <span style="font-size:12px;color:var(--text-muted);font-family:'Space Mono',monospace;">{{ $image->created_at->format('M d, Y — H:i:s') }}</span>
    </div>

    <div style="display:grid;grid-template-columns:1fr 280px;gap:16px;" id="detailGrid">

        {{-- Left: image --}}
        <div class="card fade-up" style="overflow:hidden;animation-delay:.05s;">
            <div style="position:relative;background:#000;min-height:240px;display:flex;align-items:center;justify-content:center;">
                <img id="mainImg"
                     src="{{ $image->getImageUrl() }}"
                     alt="Motion capture"
                     style="width:100%;height:auto;max-height:62vh;object-fit:contain;display:block;">

                {{-- Badge --}}
                <div style="position:absolute;top:12px;left:12px;background:{{ $sevColor }};color:{{ $severity==='warning'?'#000':'#fff' }};padding:4px 12px;border-radius:6px;font-size:10px;font-weight:700;font-family:'Space Mono',monospace;display:flex;align-items:center;gap:5px;letter-spacing:.05em;">
                    <i class="fas fa-person-running" style="font-size:9px;"></i> MOTION DETECTED
                </div>

                {{-- Fullscreen button --}}
                <button onclick="document.getElementById('mainImg').requestFullscreen()"
                        style="position:absolute;bottom:10px;right:10px;background:rgba(0,0,0,.55);border:none;color:#fff;width:34px;height:34px;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .15s;"
                        title="View fullscreen"
                        onmouseenter="this.style.background='rgba(0,0,0,.85)'"
                        onmouseleave="this.style.background='rgba(0,0,0,.55)'">
                    <i class="fas fa-expand" style="font-size:13px;"></i>
                </button>
            </div>

            {{-- Toolbar --}}
            <div style="padding:13px 16px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;border-top:1px solid var(--border);">
                <div style="display:flex;gap:16px;flex-wrap:wrap;">
                    <div style="font-size:11px;font-family:'Space Mono',monospace;">
                        <span style="color:var(--text-muted);">TIME </span><span style="color:#fff;">{{ $image->created_at->format('H:i:s') }}</span>
                    </div>
                    <div style="font-size:11px;font-family:'Space Mono',monospace;">
                        <span style="color:var(--text-muted);">DATE </span><span style="color:#fff;">{{ $image->created_at->format('M d, Y') }}</span>
                    </div>
                    @if($image->file_size)
                    <div style="font-size:11px;font-family:'Space Mono',monospace;">
                        <span style="color:var(--text-muted);">SIZE </span><span style="color:#fff;">{{ $image->getFileSizeHuman() }}</span>
                    </div>
                    @endif
                </div>

                <div style="display:flex;gap:8px;">
                    {{-- Favorite --}}
                    <button id="favBtn" onclick="toggleFav()"
                            style="display:flex;align-items:center;gap:7px;padding:7px 14px;border-radius:8px;border:1px solid {{ $image->is_favorite ? 'rgba(251,191,36,.3)' : 'var(--border)' }};background:{{ $image->is_favorite ? 'rgba(251,191,36,.08)' : 'transparent' }};cursor:pointer;font-size:12px;font-weight:600;color:{{ $image->is_favorite ? 'var(--warn)' : 'var(--text-muted)' }};font-family:'DM Sans',sans-serif;transition:all .15s;"
                            data-fav="{{ $image->is_favorite ? '1' : '0' }}">
                        <i class="fas fa-star" id="favStar" style="color:{{ $image->is_favorite ? 'var(--warn)' : 'var(--text-muted)' }};"></i>
                        <span id="favLabel">{{ $image->is_favorite ? 'Saved' : 'Save' }}</span>
                    </button>

                    {{-- Download --}}
                    <a href="{{ $image->getImageUrl() }}" download="{{ $image->filename ?? 'capture.jpg' }}"
                       style="display:inline-flex;align-items:center;gap:7px;padding:7px 14px;border-radius:8px;border:1px solid var(--border);background:transparent;text-decoration:none;font-size:12px;font-weight:600;color:var(--text-muted);transition:all .15s;"
                       onmouseenter="this.style.borderColor='var(--accent)';this.style.color='var(--accent)'"
                       onmouseleave="this.style.borderColor='var(--border)';this.style.color='var(--text-muted)'">
                        <i class="fas fa-download"></i> Download
                    </a>

                    {{-- Delete --}}
                    <form method="POST" action="{{ route('camera.delete', $image) }}" onsubmit="return confirm('Delete this capture permanently?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                style="display:flex;align-items:center;gap:7px;padding:7px 12px;border-radius:8px;border:1px solid rgba(248,113,113,.2);background:rgba(248,113,113,.08);cursor:pointer;font-size:12px;color:var(--danger);font-family:'DM Sans',sans-serif;transition:background .15s;"
                                onmouseenter="this.style.background='rgba(248,113,113,.2)'"
                                onmouseleave="this.style.background='rgba(248,113,113,.08)'">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right: sidebar --}}
        <div style="display:flex;flex-direction:column;gap:14px;">

            {{-- Linked alert --}}
            <div class="card card-p fade-up" style="animation-delay:.08s;">
                <div style="font-size:11px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-bottom:12px;"><i class="fas fa-triangle-exclamation" style="margin-right:6px;color:{{ $sevColor }};"></i>TRIGGER ALERT</div>
                @if($linkedAlert)
                    <div style="background:rgba({{ $severity==='critical'?'248,113,113':'251,191,36' }},.07);border:1px solid rgba({{ $severity==='critical'?'248,113,113':'251,191,36' }},.18);border-radius:8px;padding:12px;">
                        <div style="font-size:13px;font-weight:700;color:{{ $sevColor }};margin-bottom:4px;">
                            {{ $severity==='critical'?'🚨':'⚠️' }} {{ str_replace('_',' ',ucfirst($linkedAlert->type)) }}
                        </div>
                        <p style="font-size:12px;color:var(--text-muted);line-height:1.5;margin:0 0 10px;">{{ $linkedAlert->message }}</p>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                            <span class="alert-pill {{ $linkedAlert->severity }}">{{ strtoupper($linkedAlert->severity) }}</span>
                            <span style="font-size:10px;color:var(--text-dim);font-family:'Space Mono',monospace;">{{ $linkedAlert->created_at->diffForHumans() }}</span>
                        </div>
                        <a href="{{ route('alerts.show', $linkedAlert) }}" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center;font-size:11px;">
                            <i class="fas fa-arrow-up-right-from-square"></i> View Alert
                        </a>
                    </div>
                @else
                    <div style="text-align:center;padding:20px 10px;color:var(--text-dim);">
                        <i class="fas fa-person-running" style="font-size:28px;display:block;margin-bottom:8px;opacity:.3;"></i>
                        <p style="font-size:12px;margin:0;">Motion-triggered capture</p>
                        <p style="font-size:11px;margin:4px 0 0;color:var(--text-dim);">No linked alert record</p>
                    </div>
                @endif
            </div>

            {{-- Device info --}}
            <div class="card card-p fade-up" style="animation-delay:.12s;">
                <div style="font-size:11px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-bottom:12px;"><i class="fas fa-microchip" style="margin-right:6px;color:var(--accent);"></i>DEVICE</div>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <div>
                        <div style="font-size:10px;color:var(--text-dim);font-family:'Space Mono',monospace;margin-bottom:2px;">NAME</div>
                        <div style="font-size:13px;font-weight:600;color:#fff;">{{ $image->device->name }}</div>
                    </div>
                    <div>
                        <div style="font-size:10px;color:var(--text-dim);font-family:'Space Mono',monospace;margin-bottom:2px;">LOCATION</div>
                        <div style="font-size:12px;color:var(--text-muted);">{{ $image->device->location }}</div>
                    </div>
                    <div>
                        <div style="font-size:10px;color:var(--text-dim);font-family:'Space Mono',monospace;margin-bottom:4px;">STATUS</div>
                        <span class="status-pill {{ $image->device->status }}">
                            <span class="status-dot {{ $image->device->status }}"></span>
                            {{ ucfirst($image->device->status) }}
                        </span>
                    </div>
                </div>
                <a href="/device/{{ $image->device_id }}" class="btn btn-primary btn-sm" style="margin-top:14px;display:flex;justify-content:center;font-size:11px;">
                    <i class="fas fa-gauge-high"></i> Monitor Device
                </a>
            </div>

            {{-- Caption --}}
            @if($image->caption)
            <div class="card card-p fade-up" style="animation-delay:.16s;">
                <div style="font-size:11px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-bottom:8px;"><i class="fas fa-comment" style="margin-right:6px;"></i>NOTE</div>
                <p style="font-size:13px;color:var(--text-main);line-height:1.6;margin:0;">{{ $image->caption }}</p>
            </div>
            @endif

            {{-- Neighbor images (quick nav) --}}
            @php
                $prev = \App\Models\CameraImage::where('device_id', $image->device_id)
                    ->where('id', '<', $image->id)->latest()->first();
                $next = \App\Models\CameraImage::where('device_id', $image->device_id)
                    ->where('id', '>', $image->id)->oldest()->first();
            @endphp
            @if($prev || $next)
            <div class="card card-p fade-up" style="animation-delay:.2s;">
                <div style="font-size:11px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-bottom:10px;"><i class="fas fa-film" style="margin-right:6px;"></i>NAVIGATE</div>
                <div style="display:flex;gap:8px;">
                    @if($prev)
                    <a href="{{ route('camera.view', $prev) }}" class="btn btn-ghost btn-sm" style="flex:1;justify-content:center;font-size:11px;">
                        <i class="fas fa-chevron-left"></i> Older
                    </a>
                    @endif
                    @if($next)
                    <a href="{{ route('camera.view', $next) }}" class="btn btn-ghost btn-sm" style="flex:1;justify-content:center;font-size:11px;">
                        Newer <i class="fas fa-chevron-right"></i>
                    </a>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

{{-- Toast --}}
<div id="toast"
     style="position:fixed;bottom:24px;right:24px;z-index:2000;background:var(--bg-card);border:1px solid var(--border);border-radius:10px;padding:12px 18px;font-size:13px;font-weight:500;color:#fff;display:flex;align-items:center;gap:10px;box-shadow:0 8px 32px rgba(0,0,0,.5);transform:translateY(80px);opacity:0;transition:transform .35s cubic-bezier(.4,0,.2,1),opacity .35s;pointer-events:none;">
    <i id="toastIcon" class="fas fa-check-circle" style="font-size:16px;flex-shrink:0;"></i>
    <span id="toastMsg"></span>
</div>
@endsection

@section('scripts')
<script>
const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
const imgId  = {{ $image->id }};
let isFav    = {{ $image->is_favorite ? 'true' : 'false' }};

function toggleFav() {
    const btn   = document.getElementById('favBtn');
    const star  = document.getElementById('favStar');
    const label = document.getElementById('favLabel');
    const prev  = isFav;

    // Optimistic
    isFav = !isFav;
    applyFavUI(isFav);

    fetch(`/camera/${imgId}/favorite`, {
        method:  'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' }
    })
    .then(r => { if (!r.ok) throw new Error(); return r.json(); })
    .then(data => {
        isFav = data.is_favorite;
        applyFavUI(isFav);
        showToast(isFav ? '★ Saved to favorites' : 'Removed from favorites', isFav ? 'var(--warn)' : 'var(--text-muted)');
    })
    .catch(() => {
        isFav = prev; // rollback
        applyFavUI(isFav);
        showToast('Failed — try again', 'var(--danger)');
    });
}

function applyFavUI(fav) {
    const btn   = document.getElementById('favBtn');
    const star  = document.getElementById('favStar');
    const label = document.getElementById('favLabel');
    star.style.color        = fav ? 'var(--warn)' : 'var(--text-muted)';
    label.textContent       = fav ? 'Saved' : 'Save';
    btn.style.color         = fav ? 'var(--warn)' : 'var(--text-muted)';
    btn.style.borderColor   = fav ? 'rgba(251,191,36,.3)' : 'var(--border)';
    btn.style.background    = fav ? 'rgba(251,191,36,.08)' : 'transparent';
}

let toastT = null;
function showToast(msg, color) {
    const toast = document.getElementById('toast');
    const icon  = document.getElementById('toastIcon');
    document.getElementById('toastMsg').textContent = msg;
    icon.style.color = color || '#fff';
    icon.className   = color === 'var(--danger)' ? 'fas fa-exclamation-circle' : 'fas fa-check-circle';
    toast.style.transform = 'translateY(0)'; toast.style.opacity = '1';
    clearTimeout(toastT);
    toastT = setTimeout(() => { toast.style.transform = 'translateY(80px)'; toast.style.opacity = '0'; }, 3000);
}

// Responsive grid
function adjGrid() {
    const g = document.getElementById('detailGrid');
    if (g) g.style.gridTemplateColumns = window.innerWidth < 768 ? '1fr' : '1fr 280px';
}
adjGrid(); window.addEventListener('resize', adjGrid);
</script>
@endsection
