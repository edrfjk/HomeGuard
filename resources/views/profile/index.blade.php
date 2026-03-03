@extends('layouts.app')

@section('title', 'Profile — HomeGuard')
@section('page-title', 'My Profile')
@section('page-subtitle', 'Account information and security')

@section('content')
@php
    $tab = session('tab', old('_tab', 'account'));
@endphp

<div style="max-width:700px;display:flex;flex-direction:column;gap:20px;">

    {{-- Flash messages --}}
    @if(session('success'))
    <div style="background:rgba(52,211,153,.1);border:1px solid rgba(52,211,153,.25);color:var(--safe);padding:11px 16px;border-radius:9px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:8px;" class="fade-up">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif
    @if($errors->any() && !$errors->has('current_password'))
    <div style="background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.25);color:var(--danger);padding:11px 16px;border-radius:9px;font-size:13px;" class="fade-up">
        <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i>
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
    @endif

    {{-- Header card --}}
    <div class="card card-p fade-up" style="position:relative;overflow:hidden;">
        <div style="position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,var(--accent),transparent);"></div>
        <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
            <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,var(--accent-dim),#1e3a5f);display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;color:var(--accent);border:2px solid rgba(34,211,238,.2);flex-shrink:0;">
                {{ strtoupper(substr($user->name,0,1)) }}
            </div>
            <div style="flex:1;">
                <div style="font-size:20px;font-weight:700;color:#fff;">{{ $user->name }}</div>
                <div style="font-size:13px;color:var(--text-muted);margin-top:2px;">{{ $user->email }}</div>
                <div style="font-size:11px;color:var(--text-dim);margin-top:5px;font-family:'Space Mono',monospace;">
                    Member since {{ $user->created_at->format('F d, Y') }} · TZ: {{ $user->timezone ?? 'UTC' }}
                </div>
            </div>
        </div>
        {{-- Mini stats --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0;margin-top:18px;border-top:1px solid var(--border);padding-top:16px;">
            @foreach([
                [$user->devices()->count(), 'Devices', 'var(--accent)'],
                [$user->alerts()->where('status','active')->count(), 'Active Alerts', 'var(--warn)'],
                [$user->alerts()->count(), 'Total Alerts', 'var(--safe)'],
            ] as $s)
            <div style="text-align:center;">
                <div style="font-size:26px;font-weight:700;color:{{ $s[2] }};font-family:'Space Mono',monospace;">{{ $s[0] }}</div>
                <div style="font-size:10px;color:var(--text-muted);font-family:'Space Mono',monospace;text-transform:uppercase;letter-spacing:.07em;margin-top:3px;">{{ $s[1] }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Tab navigation --}}
    <div style="display:flex;gap:4px;background:var(--bg-card);border:1px solid var(--border);border-radius:10px;padding:4px;" class="fade-up" style="animation-delay:.05s;">
        @foreach([['account','fa-user','Account'],['security','fa-shield-halved','Security'],['danger','fa-triangle-exclamation','Danger Zone']] as $t)
        <button onclick="switchTab('{{ $t[0] }}')"
                id="tab-btn-{{ $t[0] }}"
                style="flex:1;padding:8px 12px;border-radius:7px;border:none;cursor:pointer;font-size:12px;font-weight:600;font-family:'DM Sans',sans-serif;display:flex;align-items:center;justify-content:center;gap:6px;transition:all .15s;
                {{ $tab===$t[0] ? 'background:var(--accent);color:#000;' : 'background:transparent;color:var(--text-muted);' }}">
            <i class="fas {{ $t[1] }}" style="font-size:11px;{{ $t[0]==='danger' && $tab!=='danger'?'color:var(--danger);':'' }}"></i>
            {{ $t[2] }}
        </button>
        @endforeach
    </div>

    {{-- ── TAB: Account ── --}}
    <div id="tab-account" style="display:{{ $tab==='account'?'flex':'none' }};flex-direction:column;gap:14px;">
        <div class="card card-p">
            <div style="font-size:10px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-bottom:16px;"><i class="fas fa-pen" style="margin-right:5px;color:var(--accent);"></i>EDIT PROFILE</div>
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf @method('PUT')
                <input type="hidden" name="_tab" value="account">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control {{ $errors->has('name')?'border-danger':'' }}"
                           value="{{ old('name', $user->name) }}" required>
                    @error('name')<div style="font-size:11px;color:var(--danger);margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control {{ $errors->has('email')?'border-danger':'' }}"
                           value="{{ old('email', $user->email) }}" required>
                    @error('email')<div style="font-size:11px;color:var(--danger);margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div style="display:flex;justify-content:flex-end;margin-top:4px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- Last login info --}}
        @if($lastLogin)
        <div class="card card-p">
            <div style="font-size:10px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-bottom:12px;"><i class="fas fa-clock-rotate-left" style="margin-right:5px;color:var(--accent);"></i>LAST LOGIN</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                <div class="sensor-box">
                    <div class="s-label">Time</div>
                    <div style="font-size:12px;color:#fff;margin-top:3px;font-family:'Space Mono',monospace;">{{ $lastLogin->created_at->format('M d, Y H:i') }}</div>
                </div>
                <div class="sensor-box">
                    <div class="s-label">IP Address</div>
                    <div style="font-size:12px;color:#fff;margin-top:3px;font-family:'Space Mono',monospace;">{{ $lastLogin->ip_address ?? '—' }}</div>
                </div>
                @if($lastLogin->browser)
                <div class="sensor-box">
                    <div class="s-label">Browser</div>
                    <div style="font-size:12px;color:#fff;margin-top:3px;">{{ $lastLogin->browser }}</div>
                </div>
                @endif
                @if($lastLogin->os)
                <div class="sensor-box">
                    <div class="s-label">OS</div>
                    <div style="font-size:12px;color:#fff;margin-top:3px;">{{ $lastLogin->os }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- ── TAB: Security ── --}}
    <div id="tab-security" style="display:{{ $tab==='security'?'flex':'none' }};flex-direction:column;gap:14px;">
        <div class="card card-p">
            <div style="font-size:10px;color:var(--text-muted);font-family:'Space Mono',monospace;margin-bottom:16px;"><i class="fas fa-key" style="margin-right:5px;color:var(--accent);"></i>CHANGE PASSWORD</div>

            @if($errors->has('current_password'))
            <div style="background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.25);color:var(--danger);padding:10px 14px;border-radius:8px;font-size:12px;margin-bottom:14px;">
                <i class="fas fa-exclamation-circle" style="margin-right:5px;"></i>{{ $errors->first('current_password') }}
            </div>
            @endif

            <form method="POST" action="{{ route('profile.password') }}">
                @csrf @method('PUT')
                <input type="hidden" name="_tab" value="security">
                <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <div style="position:relative;">
                        <input type="password" name="current_password" id="cp" class="form-control" placeholder="Enter current password" required style="padding-right:40px;">
                        <button type="button" onclick="togglePw('cp','cp-eye')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-muted);padding:0;">
                            <i class="fas fa-eye" id="cp-eye" style="font-size:13px;"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">New Password <span style="color:var(--text-dim);font-size:10px;">(min 8 characters)</span></label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="np" class="form-control {{ $errors->has('password')?'border-danger':'' }}" placeholder="New password" required style="padding-right:40px;">
                        <button type="button" onclick="togglePw('np','np-eye')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-muted);padding:0;">
                            <i class="fas fa-eye" id="np-eye" style="font-size:13px;"></i>
                        </button>
                    </div>
                    @error('password')<div style="font-size:11px;color:var(--danger);margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <div style="position:relative;">
                        <input type="password" name="password_confirmation" id="cnp" class="form-control" placeholder="Confirm new password" required style="padding-right:40px;">
                        <button type="button" onclick="togglePw('cnp','cnp-eye')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-muted);padding:0;">
                            <i class="fas fa-eye" id="cnp-eye" style="font-size:13px;"></i>
                        </button>
                    </div>
                </div>

                {{-- Strength meter --}}
                <div id="strengthBar" style="height:3px;background:var(--border);border-radius:3px;margin-bottom:14px;overflow:hidden;">
                    <div id="strengthFill" style="height:100%;width:0;background:var(--danger);transition:width .3s,background .3s;border-radius:3px;"></div>
                </div>
                <div id="strengthLabel" style="font-size:11px;color:var(--text-dim);font-family:'Space Mono',monospace;margin-bottom:14px;min-height:14px;"></div>

                <div style="display:flex;justify-content:flex-end;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-lock"></i> Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── TAB: Danger Zone ── --}}
    <div id="tab-danger" style="display:{{ $tab==='danger'?'flex':'none' }};flex-direction:column;gap:14px;">
        <div class="card card-p" style="border-color:rgba(248,113,113,.2);">
            <div style="font-size:10px;color:var(--danger);font-family:'Space Mono',monospace;margin-bottom:14px;"><i class="fas fa-triangle-exclamation" style="margin-right:5px;"></i>DELETE ACCOUNT</div>
            <p style="font-size:13px;color:var(--text-muted);margin:0 0 6px;line-height:1.6;">This will permanently delete your account, all registered devices, sensor data, alerts, and camera images. <strong style="color:#fff;">This cannot be undone.</strong></p>
            <p style="font-size:13px;color:var(--text-muted);margin:0 0 16px;">To confirm, type <span style="font-family:'Space Mono',monospace;color:var(--danger);font-weight:700;">DELETE</span> below:</p>
            <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return document.getElementById('confirmInput').value==='DELETE'||alert('Type DELETE to confirm')&&false">
                @csrf @method('DELETE')
                <div class="form-group">
                    <input type="text" id="confirmInput" name="confirm_delete" class="form-control"
                           placeholder="Type DELETE to confirm" autocomplete="off"
                           oninput="document.getElementById('deleteBtn').disabled=this.value!=='DELETE'"
                           style="font-family:'Space Mono',monospace;letter-spacing:.06em;">
                </div>
                <button type="submit" id="deleteBtn" disabled
                        class="btn btn-danger" style="opacity:.5;transition:opacity .2s;"
                        oninput="this.style.opacity=this.disabled?'.5':'1'">
                    <i class="fas fa-trash"></i> Delete My Account Forever
                </button>
            </form>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
// Tab switching
function switchTab(name) {
    ['account','security','danger'].forEach(t => {
        document.getElementById('tab-'+t).style.display = 'none';
        const btn = document.getElementById('tab-btn-'+t);
        btn.style.background = 'transparent';
        btn.style.color = 'var(--text-muted)';
    });
    document.getElementById('tab-'+name).style.display = 'flex';
    const active = document.getElementById('tab-btn-'+name);
    active.style.background = 'var(--accent)';
    active.style.color = '#000';
}

// Show active tab on load (from flash/error)
(function() {
    const tab = '{{ $tab }}';
    if (tab && tab !== 'account') switchTab(tab);
})();

// Delete button enable/disable
const confirmInput = document.getElementById('confirmInput');
const deleteBtn    = document.getElementById('deleteBtn');
if (confirmInput && deleteBtn) {
    confirmInput.addEventListener('input', () => {
        const ok = confirmInput.value === 'DELETE';
        deleteBtn.disabled = !ok;
        deleteBtn.style.opacity = ok ? '1' : '.5';
    });
}

// Password visibility toggle
function togglePw(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

// Password strength meter
const npInput = document.getElementById('np');
if (npInput) {
    npInput.addEventListener('input', () => {
        const v = npInput.value;
        let score = 0;
        if (v.length >= 8)  score++;
        if (v.length >= 12) score++;
        if (/[A-Z]/.test(v))  score++;
        if (/[0-9]/.test(v))  score++;
        if (/[^A-Za-z0-9]/.test(v)) score++;

        const fill  = document.getElementById('strengthFill');
        const label = document.getElementById('strengthLabel');
        const levels = [
            { pct:'20%', color:'var(--danger)', text:'Very weak' },
            { pct:'40%', color:'var(--danger)', text:'Weak' },
            { pct:'60%', color:'var(--warn)',   text:'Fair' },
            { pct:'80%', color:'#60a5fa',       text:'Good' },
            { pct:'100%',color:'var(--safe)',   text:'Strong' },
        ];
        const lvl = levels[Math.max(0, Math.min(score-1, 4))];
        if (v.length > 0) {
            fill.style.width    = lvl.pct;
            fill.style.background = lvl.color;
            label.textContent   = lvl.text;
            label.style.color   = lvl.color;
        } else {
            fill.style.width = '0';
            label.textContent = '';
        }
    });
}

// Responsive
function adj() {
    document.querySelectorAll('[style*="grid-template-columns:1fr 1fr"]').forEach(el => {
        el.style.gridTemplateColumns = window.innerWidth < 500 ? '1fr' : '1fr 1fr';
    });
}
adj(); window.addEventListener('resize', adj);
</script>
@endsection
