<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HomeGuard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --sidebar-w: 260px;
            --accent: #22d3ee;
            --accent-dim: #0e7490;
            --danger: #f87171;
            --warn: #fbbf24;
            --safe: #34d399;
            --bg-deep: #080d14;
            --bg-panel: #0f1823;
            --bg-card: #131f2e;
            --border: rgba(34,211,238,0.12);
            --text-main: #e2e8f0;
            --text-muted: #64748b;
            --text-dim: #334155;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-deep);
            color: var(--text-main);
            margin: 0;
            min-height: 100vh;
        }
        .mono { font-family: 'Space Mono', monospace; }

        /* ── Sidebar ── */
        #sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-w); height: 100vh;
            background: var(--bg-panel);
            border-right: 1px solid var(--border);
            display: flex; flex-direction: column;
            z-index: 50;
            transition: transform 0.3s cubic-bezier(.4,0,.2,1);
        }
        .sidebar-logo {
            padding: 24px 20px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 12px;
        }
        .logo-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dim) 100%);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: #000; flex-shrink: 0;
        }
        .logo-text { line-height: 1.2; }
        .logo-text h1 { font-size: 17px; font-weight: 700; color: #fff; margin: 0; letter-spacing: 0.02em; }
        .logo-text p { font-size: 10px; color: var(--accent); margin: 0; letter-spacing: 0.12em; text-transform: uppercase; font-family: 'Space Mono', monospace; }

        /* Nav */
        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .nav-section { font-size: 10px; font-weight: 600; color: var(--text-dim); letter-spacing: 0.1em; text-transform: uppercase; padding: 12px 8px 6px; font-family: 'Space Mono', monospace; }
        .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 12px; border-radius: 8px;
            color: var(--text-muted); font-size: 14px; font-weight: 500;
            text-decoration: none; transition: all 0.15s; margin-bottom: 2px;
            position: relative;
        }
        .nav-link:hover { background: rgba(34,211,238,0.06); color: var(--text-main); }
        .nav-link.active {
            background: rgba(34,211,238,0.10);
            color: var(--accent);
        }
        .nav-link.active::before {
            content: ''; position: absolute; left: 0; top: 6px; bottom: 6px;
            width: 3px; background: var(--accent); border-radius: 0 3px 3px 0;
        }
        .nav-link i { width: 18px; text-align: center; font-size: 13px; }
        .nav-badge {
            margin-left: auto; font-size: 11px; font-weight: 700;
            padding: 2px 7px; border-radius: 99px;
            font-family: 'Space Mono', monospace;
        }
        .badge-danger { background: rgba(248,113,113,0.15); color: var(--danger); }
        .badge-muted  { background: rgba(100,116,139,0.15); color: var(--text-muted); }

        /* Status dot */
        .pulse-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--safe); margin-left: auto; flex-shrink: 0;
            box-shadow: 0 0 6px var(--safe);
            animation: pulse-safe 2s infinite;
        }
        @keyframes pulse-safe { 0%,100%{opacity:1;} 50%{opacity:.4;} }

        /* User section */
        .sidebar-user {
            padding: 14px 16px;
            border-top: 1px solid var(--border);
            display: flex; align-items: center; gap: 10px;
        }
        .user-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-dim), #1e3a5f);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; color: var(--accent); flex-shrink: 0;
        }
        .user-info { flex: 1; min-width: 0; }
        .user-name { font-size: 13px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-email { font-size: 11px; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .logout-btn {
            width: 30px; height: 30px; border-radius: 8px;
            background: transparent; border: 1px solid var(--border);
            color: var(--text-muted); cursor: pointer; transition: all 0.15s;
            display: flex; align-items: center; justify-content: center; font-size: 12px;
        }
        .logout-btn:hover { background: rgba(248,113,113,0.1); color: var(--danger); border-color: var(--danger); }

        /* ── Main Content ── */
        .main-wrap { margin-left: var(--sidebar-w); min-height: 100vh; display: flex; flex-direction: column; }

        /* Top bar */
        .topbar {
            position: sticky; top: 0; z-index: 40;
            background: rgba(8,13,20,0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            padding: 14px 28px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .topbar-title h1 { font-size: 20px; font-weight: 700; color: #fff; margin: 0; }
        .topbar-title p { font-size: 12px; color: var(--text-muted); margin: 2px 0 0; }
        .topbar-actions { display: flex; align-items: center; gap: 10px; }
        .topbar-btn {
            width: 36px; height: 36px; border-radius: 8px;
            background: var(--bg-card); border: 1px solid var(--border);
            color: var(--text-muted); cursor: pointer; transition: all 0.15s;
            display: flex; align-items: center; justify-content: center; font-size: 14px;
            text-decoration: none; position: relative;
        }
        .topbar-btn:hover { border-color: var(--accent); color: var(--accent); }
        .topbar-notif-badge {
            position: absolute; top: -4px; right: -4px;
            width: 16px; height: 16px; border-radius: 50%;
            background: var(--danger); color: #fff;
            font-size: 9px; font-weight: 700; font-family: 'Space Mono', monospace;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid var(--bg-deep);
        }

        /* Page content */
        .page-content { flex: 1; padding: 28px; }

        /* ── Cards ── */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
        }
        .card-p { padding: 24px; }

        /* ── Stat Cards ── */
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 22px;
            position: relative; overflow: hidden;
            transition: border-color 0.2s, transform 0.2s;
        }
        .stat-card:hover { border-color: rgba(34,211,238,0.3); transform: translateY(-2px); }
        .stat-card::after {
            content: ''; position: absolute;
            top: 0; left: 0; right: 0; height: 2px;
        }
        .stat-card.blue::after  { background: linear-gradient(90deg, var(--accent), transparent); }
        .stat-card.green::after { background: linear-gradient(90deg, var(--safe), transparent); }
        .stat-card.orange::after{ background: linear-gradient(90deg, var(--warn), transparent); }
        .stat-card.red::after   { background: linear-gradient(90deg, var(--danger), transparent); }

        .stat-label { font-size: 11px; color: var(--text-muted); letter-spacing: 0.08em; text-transform: uppercase; font-family: 'Space Mono', monospace; }
        .stat-value { font-size: 36px; font-weight: 700; font-family: 'Space Mono', monospace; line-height: 1.1; margin: 8px 0 4px; }
        .stat-value.blue   { color: var(--accent); }
        .stat-value.green  { color: var(--safe); }
        .stat-value.orange { color: var(--warn); }
        .stat-value.red    { color: var(--danger); }
        .stat-icon {
            position: absolute; top: 22px; right: 22px;
            width: 42px; height: 42px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; font-size: 18px;
        }
        .stat-icon.blue   { background: rgba(34,211,238,0.1);  color: var(--accent); }
        .stat-icon.green  { background: rgba(52,211,153,0.1);  color: var(--safe); }
        .stat-icon.orange { background: rgba(251,191,36,0.1);  color: var(--warn); }
        .stat-icon.red    { background: rgba(248,113,113,0.1); color: var(--danger); }
        .stat-sub { font-size: 11px; color: var(--text-muted); display: flex; align-items: center; gap: 4px; }

        /* ── Status pill ── */
        .status-pill {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 10px; border-radius: 99px; font-size: 11px; font-weight: 600;
            font-family: 'Space Mono', monospace;
        }
        .status-pill.online  { background: rgba(52,211,153,0.12); color: var(--safe); }
        .status-pill.offline { background: rgba(100,116,139,0.12); color: var(--text-muted); }
        .status-dot { width: 6px; height: 6px; border-radius: 50%; }
        .status-dot.online  { background: var(--safe); animation: pulse-safe 2s infinite; }
        .status-dot.offline { background: var(--text-dim); }

        /* ── Alert pills ── */
        .alert-pill {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 2px 8px; border-radius: 99px; font-size: 10px; font-weight: 700;
            font-family: 'Space Mono', monospace;
        }
        .alert-pill.critical { background: rgba(248,113,113,0.15); color: var(--danger); }
        .alert-pill.warning  { background: rgba(251,191,36,0.12);  color: var(--warn); }
        .alert-pill.info     { background: rgba(34,211,238,0.1);   color: var(--accent); }

        /* ── Sensor value boxes ── */
        .sensor-box {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 10px; text-align: center;
        }
        .sensor-box .s-label { font-size: 10px; color: var(--text-muted); font-family: 'Space Mono', monospace; text-transform: uppercase; letter-spacing: 0.08em; }
        .sensor-box .s-val   { font-size: 20px; font-weight: 700; font-family: 'Space Mono', monospace; margin-top: 4px; }

        /* ── Buttons ── */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 600;
            text-decoration: none; cursor: pointer; border: none; transition: all 0.15s;
        }
        .btn-primary {
            background: var(--accent); color: #000;
        }
        .btn-primary:hover { background: #67e8f9; }
        .btn-ghost {
            background: transparent; color: var(--text-muted);
            border: 1px solid var(--border);
        }
        .btn-ghost:hover { border-color: var(--accent); color: var(--accent); }
        .btn-danger { background: rgba(248,113,113,0.12); color: var(--danger); border: 1px solid rgba(248,113,113,0.2); }
        .btn-danger:hover { background: rgba(248,113,113,0.22); }
        .btn-sm { padding: 6px 12px; font-size: 12px; }

        /* ── Alert list items ── */
        .alert-item {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 12px 14px; border-radius: 10px; margin-bottom: 6px;
            border-left: 3px solid transparent; transition: background 0.15s;
            text-decoration: none; color: inherit;
        }
        .alert-item:hover { background: rgba(255,255,255,0.03); }
        .alert-item.critical { border-left-color: var(--danger); background: rgba(248,113,113,0.04); }
        .alert-item.warning  { border-left-color: var(--warn);   background: rgba(251,191,36,0.04); }
        .alert-item.info     { border-left-color: var(--accent);  background: rgba(34,211,238,0.04); }

        /* ── Form controls ── */
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 12px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.06em; font-family: 'Space Mono', monospace; }
        .form-control {
            width: 100%; padding: 10px 14px;
            background: rgba(255,255,255,0.04); border: 1px solid var(--border);
            border-radius: 8px; color: var(--text-main); font-size: 14px;
            transition: border-color 0.15s; font-family: 'DM Sans', sans-serif;
        }
        .form-control:focus { outline: none; border-color: var(--accent); background: rgba(34,211,238,0.04); }
        .form-control::placeholder { color: var(--text-dim); }

        /* ── Mobile ── */
        .mobile-menu-btn {
            display: none; background: var(--bg-card); border: 1px solid var(--border);
            color: var(--text-main); padding: 8px 12px; border-radius: 8px;
            cursor: pointer; font-size: 16px;
        }
        @media (max-width: 1023px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            .main-wrap { margin-left: 0; }
            .mobile-menu-btn { display: flex; align-items: center; }
            .page-content { padding: 16px; }
            .topbar { padding: 12px 16px; }
        }

        /* ── Overlay ── */
        #sidebarOverlay {
            display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6);
            z-index: 45; backdrop-filter: blur(2px);
        }
        #sidebarOverlay.show { display: block; }

        /* ── Transitions ── */
        @keyframes fadeUp { from{opacity:0;transform:translateY(12px);} to{opacity:1;transform:none;} }
        .fade-up { animation: fadeUp 0.4s ease-out forwards; opacity: 0; }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--text-dim); border-radius: 99px; }

        /* ── Flash messages ── */
        .flash {
            padding: 12px 18px; border-radius: 8px; font-size: 13px; font-weight: 500;
            display: flex; align-items: center; gap: 10px; margin-bottom: 20px;
        }
        .flash.success { background: rgba(52,211,153,0.1); color: var(--safe); border: 1px solid rgba(52,211,153,0.2); }
        .flash.error   { background: rgba(248,113,113,0.1); color: var(--danger); border: 1px solid rgba(248,113,113,0.2); }

        /* ── Table ── */
        table { width: 100%; border-collapse: collapse; }
        th { font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; font-family: 'Space Mono', monospace; padding: 8px 14px; border-bottom: 1px solid var(--border); text-align: left; }
        td { padding: 12px 14px; border-bottom: 1px solid rgba(255,255,255,0.03); font-size: 13px; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(255,255,255,0.02); }
    </style>
</head>
<body>
<div id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- ── Sidebar ── -->
<aside id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon"><i class="fas fa-shield-halved"></i></div>
        <div class="logo-text">
            <h1>HomeGuard</h1>
            <p>IoT Safety System</p>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Monitor</div>

        <a href="/dashboard" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="fas fa-gauge-high"></i>
            <span>Dashboard</span>
        </a>

        <a href="/devices" class="nav-link {{ request()->is('devices*') || request()->is('device/*') ? 'active' : '' }}">
            <i class="fas fa-microchip"></i>
            <span>Devices</span>
            @php $devCount = auth()->user()->devices()->where('is_active', true)->count(); @endphp
            <span class="nav-badge badge-muted">{{ $devCount }}</span>
        </a>

        @php $activeAlerts = auth()->user()->alerts()->where('status', 'active')->count(); @endphp
        <a href="/alerts" class="nav-link {{ request()->is('alerts*') ? 'active' : '' }}">
            <i class="fas fa-bell"></i>
            <span>Alerts</span>
            @if($activeAlerts > 0)
                <span class="nav-badge badge-danger">{{ $activeAlerts }}</span>
            @else
                <span class="nav-badge badge-muted">0</span>
            @endif
        </a>

        <div class="nav-section" style="margin-top:8px;">Account</div>

        <a href="/profile" class="nav-link {{ request()->is('profile') ? 'active' : '' }}">
            <i class="fas fa-user-circle"></i>
            <span>Profile</span>
        </a>

        <a href="/settings" class="nav-link {{ request()->is('settings*') ? 'active' : '' }}">
            <i class="fas fa-sliders"></i>
            <span>Settings</span>
        </a>
    </nav>

    <div class="sidebar-user">
        <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        <div class="user-info">
            <div class="user-name">{{ auth()->user()->name }}</div>
            <div class="user-email">{{ auth()->user()->email }}</div>
        </div>
        <form method="POST" action="/logout">
            @csrf
            <button type="submit" class="logout-btn" title="Logout">
                <i class="fas fa-right-from-bracket"></i>
            </button>
        </form>
    </div>
</aside>

<!-- ── Main ── -->
<div class="main-wrap">
    <!-- Top bar -->
    <header class="topbar">
        <div class="flex items-center gap-3">
            <button class="mobile-menu-btn" onclick="openSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div class="topbar-title">
                <h1>@yield('page-title', 'Dashboard')</h1>
                <p>@yield('page-subtitle', '')</p>
            </div>
        </div>

        <div class="topbar-actions">
            <!-- Alerts bell -->
            <a href="/alerts" class="topbar-btn" title="Alerts">
                <i class="fas fa-bell"></i>
                @if($activeAlerts > 0)
                    <span class="topbar-notif-badge">{{ min($activeAlerts, 9) }}</span>
                @endif
            </a>
            <!-- Time -->
            <div class="mono" style="font-size:12px;color:var(--text-muted);padding:0 4px;" id="clock"></div>
        </div>
    </header>

    <!-- Flash messages -->
    <div style="padding: 0 28px; padding-top: 20px;">
        @if(session('success'))
            <div class="flash success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="flash error">
                <i class="fas fa-triangle-exclamation"></i>
                <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            </div>
        @endif
    </div>

    <!-- Page Content -->
    <main class="page-content">
        @yield('content')
    </main>
</div>

<script>
// Clock
function updateClock() {
    const el = document.getElementById('clock');
    if (el) el.textContent = new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit', second:'2-digit'});
}
updateClock();
setInterval(updateClock, 1000);

// Mobile sidebar
function openSidebar() {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('sidebarOverlay').classList.add('show');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('show');
}

// Auto-dismiss flash
setTimeout(() => {
    document.querySelectorAll('.flash').forEach(el => {
        el.style.transition = 'opacity 0.5s'; el.style.opacity = '0';
        setTimeout(() => el.remove(), 500);
    });
}, 4000);
</script>

@yield('scripts')
</body>
</html>
