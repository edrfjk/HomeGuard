@extends('layouts.app')

@section('title', $device->name . ' — Camera Gallery — HomeGuard')
@section('page-title', 'Motion Captures')
@section('page-subtitle', $device->name . ' · ' . $device->location)

@section('content')
<div style="display:flex;flex-direction:column;gap:20px;">

    {{-- ── Header Row ── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;" class="fade-up">
        <a href="/device/{{ $device->id }}" class="btn btn-ghost btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Device
        </a>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <span style="background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.2);color:var(--danger);padding:5px 12px;border-radius:99px;font-size:11px;font-family:'Space Mono',monospace;font-weight:700;">
                <i class="fas fa-camera" style="margin-right:5px;"></i>{{ $totalCount }} captures
            </span>
            <span id="favCountBadge" style="background:rgba(251,191,36,.1);border:1px solid rgba(251,191,36,.2);color:var(--warn);padding:5px 12px;border-radius:99px;font-size:11px;font-family:'Space Mono',monospace;font-weight:700;">
                <i class="fas fa-star" style="margin-right:5px;"></i><span id="favCount">{{ $favCount }}</span> saved
            </span>
        </div>
    </div>

    {{-- ── Filters ── --}}
    <div class="card card-p fade-up" style="animation-delay:.05s;padding:14px 18px;">
        <div style="display:flex;align-items:center;flex-wrap:wrap;gap:10px;">
            <div style="position:relative;flex:1;min-width:180px;">
                <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:12px;pointer-events:none;"></i>
                <input type="text" id="searchBox" placeholder="Search by date or caption..."
                       oninput="clientSearch()"
                       style="width:100%;padding:8px 12px 8px 34px;background:rgba(255,255,255,.04);border:1px solid var(--border);border-radius:8px;color:var(--text-main);font-size:13px;font-family:'DM Sans',sans-serif;outline:none;transition:border-color .15s;"
                       onfocus="this.style.borderColor='var(--accent)'"
                       onblur="this.style.borderColor='var(--border)'">
            </div>
            <div style="display:flex;gap:6px;">
                <a href="?filter=all" class="btn btn-sm {{ $filter==='all'?'btn-primary':'btn-ghost' }}">
                    <i class="fas fa-grid-2"></i> All
                </a>
                <a href="?filter=favorites" class="btn btn-sm {{ $filter==='favorites'?'btn-primary':'btn-ghost' }}">
                    <i class="fas fa-star"></i> Saved
                </a>
            </div>
            <div style="font-size:11px;color:var(--text-dim);font-family:'Space Mono',monospace;white-space:nowrap;margin-left:auto;">
                Newest first · {{ $images->total() }} shown
            </div>
        </div>
    </div>

    {{-- ── Empty state ── --}}
    @if($images->isEmpty())
        <div class="card card-p fade-up" style="text-align:center;padding:70px 20px;">
            @if($filter === 'favorites')
                <i class="fas fa-star" style="font-size:44px;display:block;color:var(--text-dim);margin-bottom:16px;opacity:.4;"></i>
                <p style="font-size:16px;font-weight:700;color:#fff;margin:0 0 8px;">No saved captures</p>
                <p style="font-size:13px;color:var(--text-muted);margin:0 0 20px;">Star any image to save it here</p>
                <a href="?filter=all" class="btn btn-ghost btn-sm">View all captures</a>
            @else
                <i class="fas fa-camera" style="font-size:44px;display:block;color:var(--text-dim);margin-bottom:16px;opacity:.4;"></i>
                <p style="font-size:16px;font-weight:700;color:#fff;margin:0 0 8px;">No captures yet</p>
                <p style="font-size:13px;color:var(--text-muted);margin:0;">Images will appear here when your ESP32-CAM detects motion</p>
            @endif
        </div>
    @else

    {{-- ── Gallery grid ── --}}
    <div id="gallery" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px;" class="fade-up" style="animation-delay:.1s;">
        @foreach($images as $img)
        @php
            $linkedAlert   = $img->alert;
            $severity      = $linkedAlert?->severity ?? 'warning';
            $sevColor      = $severity === 'critical' ? 'var(--danger)' : 'var(--warn)';
            $sevBg         = $severity === 'critical' ? 'rgba(248,113,113,.08)' : 'rgba(251,191,36,.07)';
            $sevBorder     = $severity === 'critical' ? 'rgba(248,113,113,.2)' : 'rgba(251,191,36,.18)';
        @endphp
        <div class="img-card"
             data-caption="{{ strtolower($img->caption ?? '') }}"
             data-date="{{ strtolower($img->created_at->format('M d Y H i')) }}"
             style="position:relative;border-radius:12px;overflow:hidden;border:1px solid var(--border);background:var(--bg-card);display:flex;flex-direction:column;transition:border-color .2s,transform .2s,box-shadow .2s;"
             onmouseenter="this.style.borderColor='rgba(34,211,238,.28)';this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 30px rgba(0,0,0,.35)'"
             onmouseleave="this.style.borderColor='';this.style.transform='';this.style.boxShadow=''">

            {{-- Thumbnail --}}
            <div style="position:relative;aspect-ratio:4/3;overflow:hidden;background:#000;cursor:pointer;"
                 onclick="openModal({{ $img->id }}, '{{ $img->getImageUrl() }}', '{{ addslashes($img->created_at->format('M d, Y — H:i:s')) }}', {{ $img->is_favorite ? 'true' : 'false' }})">

                <img src="{{ $img->getImageUrl() }}"
                     alt="Motion capture"
                     loading="lazy"
                     style="width:100%;height:100%;object-fit:cover;transition:transform .4s;display:block;"
                     onmouseenter="this.style.transform='scale(1.07)'"
                     onmouseleave="this.style.transform=''">

                {{-- Zoom overlay --}}
                <div style="position:absolute;inset:0;background:rgba(0,0,0,0);display:flex;align-items:center;justify-content:center;transition:background .2s;pointer-events:none;"
                     class="zoom-ov">
                    <div style="background:rgba(255,255,255,.15);backdrop-filter:blur(4px);border-radius:50%;width:44px;height:44px;display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity .2s;" class="zoom-icon">
                        <i class="fas fa-expand" style="font-size:16px;color:#fff;"></i>
                    </div>
                </div>

                {{-- Motion badge --}}
                <div style="position:absolute;top:8px;left:8px;background:{{ $sevColor }};color:{{ $severity==='warning'?'#000':'#fff' }};padding:3px 9px;border-radius:5px;font-size:9px;font-weight:700;font-family:'Space Mono',monospace;display:flex;align-items:center;gap:4px;letter-spacing:.05em;">
                    <i class="fas fa-person-running" style="font-size:9px;"></i>
                    {{ strtoupper($severity) }}
                </div>

                {{-- Favorite button --}}
                <button class="fav-btn"
                        id="fav-{{ $img->id }}"
                        data-id="{{ $img->id }}"
                        data-fav="{{ $img->is_favorite ? '1' : '0' }}"
                        onclick="event.stopPropagation(); toggleFav({{ $img->id }}, this)"
                        title="{{ $img->is_favorite ? 'Remove from saved' : 'Save this capture' }}"
                        style="position:absolute;top:7px;right:7px;background:rgba(0,0,0,.55);border:none;cursor:pointer;width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;z-index:2;transition:transform .15s,background .15s;"
                        onmouseenter="this.style.transform='scale(1.2)';this.style.background='rgba(0,0,0,.8)'"
                        onmouseleave="this.style.transform='';this.style.background='rgba(0,0,0,.55)'">
                    <i class="fas fa-star" style="font-size:13px;color:{{ $img->is_favorite ? 'var(--warn)' : 'rgba(255,255,255,.5)' }};transition:color .15s;"></i>
                </button>
            </div>

            {{-- Card body --}}
            <div style="padding:11px 12px;flex:1;display:flex;flex-direction:column;gap:7px;">
                {{-- Time & size --}}
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:11px;color:var(--text-muted);font-family:'Space Mono',monospace;">
                        <i class="fas fa-clock" style="margin-right:4px;font-size:10px;"></i>{{ $img->created_at->diffForHumans() }}
                    </span>
                    @if($img->file_size)
                    <span style="font-size:10px;color:var(--text-dim);font-family:'Space Mono',monospace;">{{ $img->getFileSizeHuman() }}</span>
                    @endif
                </div>

                {{-- Caption --}}
                @if($img->caption)
                <p style="font-size:12px;color:var(--text-muted);margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $img->caption }}</p>
                @endif

                {{-- Linked alert chip --}}
                @if($linkedAlert)
                <a href="{{ route('alerts.show', $linkedAlert) }}"
                   onclick="event.stopPropagation()"
                   style="display:flex;align-items:center;gap:6px;background:{{ $sevBg }};border:1px solid {{ $sevBorder }};border-radius:6px;padding:5px 9px;text-decoration:none;">
                    <span style="font-size:12px;">{{ $severity==='critical'?'🚨':'⚠️' }}</span>
                    <span style="font-size:10px;color:{{ $sevColor }};font-family:'Space Mono',monospace;font-weight:700;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        {{ str_replace('_',' ',ucfirst($linkedAlert->type)) }}
                    </span>
                    <i class="fas fa-arrow-up-right-from-square" style="font-size:9px;color:var(--text-dim);flex-shrink:0;"></i>
                </a>
                @else
                <div style="display:flex;align-items:center;gap:6px;background:rgba(100,116,139,.06);border:1px solid rgba(100,116,139,.12);border-radius:6px;padding:5px 9px;">
                    <span style="font-size:10px;color:var(--text-dim);font-family:'Space Mono',monospace;">Motion triggered</span>
                </div>
                @endif

                {{-- Action buttons --}}
                <div style="display:flex;gap:6px;margin-top:2px;">
                    <button onclick="openModal({{ $img->id }}, '{{ $img->getImageUrl() }}', '{{ addslashes($img->created_at->format('M d, Y — H:i:s')) }}', {{ $img->is_favorite ? 'true' : 'false' }})"
                            class="btn btn-ghost btn-sm" style="flex:1;justify-content:center;font-size:11px;padding:6px 8px;">
                        <i class="fas fa-expand"></i> View
                    </button>
                    <button onclick="confirmDelete({{ $img->id }}, '{{ $img->created_at->format('M d H:i') }}')"
                            class="btn btn-danger btn-sm" style="font-size:11px;padding:6px 9px;" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- No search results message --}}
    <div id="noSearchResults" style="display:none;text-align:center;padding:40px;color:var(--text-muted);">
        <i class="fas fa-search" style="font-size:32px;display:block;margin-bottom:12px;opacity:.3;"></i>
        <p style="font-size:14px;">No captures match your search</p>
    </div>

    {{-- Pagination --}}
    @if($images->hasPages())
    <div style="display:flex;justify-content:center;align-items:center;gap:6px;flex-wrap:wrap;margin-top:4px;" class="fade-up">
        @if($images->onFirstPage())
            <span class="btn btn-ghost btn-sm" style="opacity:.35;cursor:default;"><i class="fas fa-chevron-left"></i></span>
        @else
            <a href="{{ $images->previousPageUrl() }}" class="btn btn-ghost btn-sm"><i class="fas fa-chevron-left"></i></a>
        @endif

        @foreach($images->getUrlRange(1, $images->lastPage()) as $page => $url)
            @if($page == $images->currentPage())
                <span class="btn btn-sm" style="background:var(--accent);color:#000;cursor:default;min-width:36px;justify-content:center;">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="btn btn-ghost btn-sm" style="min-width:36px;justify-content:center;">{{ $page }}</a>
            @endif
        @endforeach

        @if($images->hasMorePages())
            <a href="{{ $images->nextPageUrl() }}" class="btn btn-ghost btn-sm"><i class="fas fa-chevron-right"></i></a>
        @else
            <span class="btn btn-ghost btn-sm" style="opacity:.35;cursor:default;"><i class="fas fa-chevron-right"></i></span>
        @endif
    </div>
    @endif

    @endif {{-- end @if images not empty --}}
</div>

{{-- ── Full-screen Image Modal ── --}}
<div id="imgModal"
     style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.96);flex-direction:column;align-items:center;justify-content:center;padding:16px;"
     onclick="closeModal()">

    <div onclick="event.stopPropagation()" style="display:flex;flex-direction:column;align-items:center;gap:12px;max-width:94vw;">
        {{-- Image --}}
        <div style="position:relative;border-radius:10px;overflow:hidden;box-shadow:0 0 80px rgba(0,0,0,.8);">
            <img id="modalImg" style="max-width:90vw;max-height:68vh;object-fit:contain;display:block;border-radius:10px;">
            <div style="position:absolute;top:10px;left:10px;background:var(--warn);color:#000;padding:3px 10px;border-radius:5px;font-size:9px;font-weight:700;font-family:'Space Mono',monospace;display:flex;align-items:center;gap:4px;">
                <i class="fas fa-person-running" style="font-size:9px;"></i> MOTION DETECTED
            </div>
        </div>

        {{-- Modal toolbar --}}
        <div style="display:flex;align-items:center;justify-content:space-between;width:100%;flex-wrap:wrap;gap:10px;background:rgba(15,24,35,.92);backdrop-filter:blur(10px);border:1px solid var(--border);border-radius:10px;padding:11px 16px;">
            <div style="font-size:12px;color:var(--text-muted);font-family:'Space Mono',monospace;" id="modalTime"></div>
            <div style="display:flex;gap:8px;align-items:center;">
                {{-- Save/unsave --}}
                <button id="modalFavBtn" onclick="modalToggleFav()"
                        style="display:flex;align-items:center;gap:7px;padding:7px 14px;border-radius:8px;border:1px solid var(--border);background:transparent;cursor:pointer;font-size:12px;font-weight:600;color:var(--text-muted);font-family:'DM Sans',sans-serif;transition:all .15s;">
                    <i class="fas fa-star" id="modalFavStar"></i>
                    <span id="modalFavLabel">Save</span>
                </button>
                {{-- Download --}}
                <a id="modalDlBtn" href="#" download
                   style="display:inline-flex;align-items:center;gap:7px;padding:7px 14px;border-radius:8px;border:1px solid var(--border);background:transparent;text-decoration:none;font-size:12px;font-weight:600;color:var(--text-muted);transition:all .15s;"
                   onmouseenter="this.style.borderColor='var(--accent)';this.style.color='var(--accent)'"
                   onmouseleave="this.style.borderColor='var(--border)';this.style.color='var(--text-muted)'">
                    <i class="fas fa-download"></i> Download
                </a>
                {{-- Delete --}}
                <button id="modalDeleteBtn" onclick="modalDelete()"
                        style="display:flex;align-items:center;gap:7px;padding:7px 12px;border-radius:8px;border:1px solid rgba(248,113,113,.2);background:rgba(248,113,113,.08);cursor:pointer;font-size:12px;color:var(--danger);font-family:'DM Sans',sans-serif;transition:background .15s;"
                        onmouseenter="this.style.background='rgba(248,113,113,.2)'"
                        onmouseleave="this.style.background='rgba(248,113,113,.08)'">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Close --}}
    <button onclick="closeModal()"
            style="position:absolute;top:16px;right:16px;background:rgba(255,255,255,.1);border:none;color:#fff;width:38px;height:38px;border-radius:50%;cursor:pointer;font-size:15px;display:flex;align-items:center;justify-content:center;transition:background .15s;"
            onmouseenter="this.style.background='rgba(255,255,255,.22)'"
            onmouseleave="this.style.background='rgba(255,255,255,.1)'">
        <i class="fas fa-xmark"></i>
    </button>
</div>

{{-- Toast --}}
<div id="toast"
     style="position:fixed;bottom:24px;right:24px;z-index:2000;background:var(--bg-card);border:1px solid var(--border);border-radius:10px;padding:12px 18px;font-size:13px;font-weight:500;color:#fff;display:flex;align-items:center;gap:10px;box-shadow:0 8px 32px rgba(0,0,0,.5);transform:translateY(80px);opacity:0;transition:transform .35s cubic-bezier(.4,0,.2,1),opacity .35s;pointer-events:none;max-width:280px;">
    <i id="toastIcon" class="fas fa-check-circle" style="font-size:16px;flex-shrink:0;"></i>
    <span id="toastMsg"></span>
</div>

@endsection

@section('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let modalImageId = null;
let modalIsFav   = false;

// ── Hover effects on cards (for zoom overlay) ──
document.querySelectorAll('.img-card').forEach(card => {
    const ov   = card.querySelector('.zoom-ov');
    const icon = card.querySelector('.zoom-icon');
    if (!ov || !icon) return;
    card.addEventListener('mouseenter', () => { ov.style.background = 'rgba(0,0,0,.3)'; icon.style.opacity = '1'; });
    card.addEventListener('mouseleave', () => { ov.style.background = 'rgba(0,0,0,0)'; icon.style.opacity = '0'; });
});

// ── Client-side search (current page) ──
function clientSearch() {
    const q = document.getElementById('searchBox').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.img-card');
    let visible = 0;
    cards.forEach(c => {
        const match = !q || (c.dataset.caption || '').includes(q) || (c.dataset.date || '').includes(q);
        c.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    const nr = document.getElementById('noSearchResults');
    if (nr) nr.style.display = visible === 0 ? 'block' : 'none';
}

// ── Toggle favorite ──
function toggleFav(id, btn) {
    const icon   = btn.querySelector('i');
    const wasFav = btn.dataset.fav === '1';
    const newFav = !wasFav;

    // Optimistic update
    icon.style.color = newFav ? 'var(--warn)' : 'rgba(255,255,255,.5)';
    btn.dataset.fav  = newFav ? '1' : '0';
    btn.title        = newFav ? 'Remove from saved' : 'Save this capture';

    fetch(`/camera/${id}/favorite`, {
        method:  'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' }
    })
    .then(r => { if (!r.ok) throw new Error(); return r.json(); })
    .then(data => {
        const isFav = data.is_favorite;
        icon.style.color = isFav ? 'var(--warn)' : 'rgba(255,255,255,.5)';
        btn.dataset.fav  = isFav ? '1' : '0';
        btn.title        = isFav ? 'Remove from saved' : 'Save this capture';

        if (modalImageId == id) updateModalFavUI(isFav);
        adjustFavBadge(isFav ? 1 : -1);
        showToast(isFav ? '★ Saved to favorites' : 'Removed from favorites', isFav ? 'var(--warn)' : 'var(--text-muted)');
    })
    .catch(() => {
        // Rollback
        icon.style.color = wasFav ? 'var(--warn)' : 'rgba(255,255,255,.5)';
        btn.dataset.fav  = wasFav ? '1' : '0';
        showToast('Failed — please try again', 'var(--danger)');
    });
}

function adjustFavBadge(delta) {
    const el = document.getElementById('favCount');
    if (el) el.textContent = Math.max(0, parseInt(el.textContent || 0) + delta);
}

// ── Modal ──
function openModal(id, url, time, isFav) {
    modalImageId = id;
    modalIsFav   = isFav;
    document.getElementById('modalImg').src    = url;
    document.getElementById('modalTime').textContent = time;
    document.getElementById('modalDlBtn').href = url;
    updateModalFavUI(isFav);
    document.getElementById('imgModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('imgModal').style.display = 'none';
    document.body.style.overflow = '';
}

function updateModalFavUI(isFav) {
    modalIsFav = isFav;
    const star  = document.getElementById('modalFavStar');
    const label = document.getElementById('modalFavLabel');
    const btn   = document.getElementById('modalFavBtn');
    star.style.color  = isFav ? 'var(--warn)' : 'var(--text-muted)';
    label.textContent = isFav ? 'Saved' : 'Save';
    btn.style.borderColor = isFav ? 'rgba(251,191,36,.35)' : 'var(--border)';
    btn.style.color       = isFav ? 'var(--warn)' : 'var(--text-muted)';
    btn.style.background  = isFav ? 'rgba(251,191,36,.08)' : 'transparent';
}

function modalToggleFav() {
    if (!modalImageId) return;
    const cardBtn = document.getElementById(`fav-${modalImageId}`);
    if (cardBtn) {
        toggleFav(modalImageId, cardBtn);
    } else {
        // Not on this page — call directly
        fetch(`/camera/${modalImageId}/favorite`, {
            method:  'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        }).then(r => r.json()).then(data => {
            updateModalFavUI(data.is_favorite);
            adjustFavBadge(data.is_favorite ? 1 : -1);
            showToast(data.is_favorite ? '★ Saved' : 'Removed', data.is_favorite ? 'var(--warn)' : 'var(--text-muted)');
        }).catch(() => showToast('Failed', 'var(--danger)'));
    }
}

// ── Delete ──
function confirmDelete(id, label) {
    if (!confirm(`Delete the capture from "${label}"?\nThis cannot be undone.`)) return;
    doDelete(id);
}

function modalDelete() {
    if (!modalImageId) return;
    if (!confirm('Delete this capture permanently?')) return;
    const id = modalImageId;
    closeModal();
    doDelete(id);
}

function doDelete(id) {
    fetch(`/camera/${id}`, {
        method:  'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
        body:    '_method=DELETE'
    })
    .then(r => { if (!r.ok) throw new Error(); return r.json(); })
    .then(() => {
        const card = document.querySelector(`#fav-${id}`)?.closest('.img-card');
        if (card) {
            card.style.transition = 'opacity .35s, transform .35s';
            card.style.opacity    = '0';
            card.style.transform  = 'scale(.88)';
            setTimeout(() => card.remove(), 350);
        }
        showToast('Image deleted', 'var(--safe)');
    })
    .catch(() => showToast('Delete failed — try again', 'var(--danger)'));
}

// ── Toast ──
let toastT = null;
function showToast(msg, color) {
    const toast = document.getElementById('toast');
    const icon  = document.getElementById('toastIcon');
    document.getElementById('toastMsg').textContent = msg;
    icon.style.color = color || '#fff';
    icon.className   = (color === 'var(--danger)') ? 'fas fa-exclamation-circle' : 'fas fa-check-circle';
    toast.style.transform = 'translateY(0)';
    toast.style.opacity   = '1';
    clearTimeout(toastT);
    toastT = setTimeout(() => { toast.style.transform = 'translateY(80px)'; toast.style.opacity = '0'; }, 3000);
}

// ── Keyboard support ──
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
</script>
@endsection
