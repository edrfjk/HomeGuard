<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomeGuard — IoT Safety System</title>
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --accent: #22d3ee; --accent-dim: #0e7490;
            --bg: #080d14; --panel: #0f1823; --card: #131f2e;
            --border: rgba(34,211,238,0.12);
            --text: #e2e8f0; --muted: #64748b; --dim: #1e293b;
            --safe: #34d399; --warn: #fbbf24; --danger: #f87171;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg); color: var(--text);
            min-height: 100vh; overflow-x: hidden;
        }

        /* ── Grid bg ── */
        .grid-bg {
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            background-image:
                linear-gradient(rgba(34,211,238,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(34,211,238,.03) 1px, transparent 1px);
            background-size: 60px 60px;
        }
        .orb {
            position: fixed; border-radius: 50%; pointer-events: none; z-index: 0;
            filter: blur(80px); opacity: .4;
        }
        .orb-1 { width: 600px; height: 600px; top: -200px; left: -100px; background: radial-gradient(circle, rgba(14,116,144,.5), transparent 70%); }
        .orb-2 { width: 400px; height: 400px; bottom: -100px; right: -100px; background: radial-gradient(circle, rgba(34,211,238,.2), transparent 70%); }

        /* ── Nav ── */
        nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            padding: 18px 40px;
            display: flex; align-items: center; justify-content: space-between;
            background: rgba(8,13,20,.8); backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
        }
        .nav-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .nav-logo-icon {
            width: 36px; height: 36px; border-radius: 9px;
            background: linear-gradient(135deg, var(--accent), var(--accent-dim));
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; color: #000;
        }
        .nav-logo-text { font-size: 18px; font-weight: 700; color: #fff; }
        .nav-links { display: flex; align-items: center; gap: 8px; }
        .nav-btn {
            padding: 8px 18px; border-radius: 8px; font-size: 13px; font-weight: 600;
            text-decoration: none; transition: all .15s; cursor: pointer;
        }
        .nav-btn-ghost { color: var(--muted); border: 1px solid var(--border); background: transparent; }
        .nav-btn-ghost:hover { color: var(--accent); border-color: var(--accent); }
        .nav-btn-primary { background: var(--accent); color: #000; }
        .nav-btn-primary:hover { background: #67e8f9; box-shadow: 0 0 20px rgba(34,211,238,.3); }

        /* ── Hero ── */
        .hero {
            position: relative; z-index: 10;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            text-align: center; padding: 120px 24px 80px;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(34,211,238,.08); border: 1px solid rgba(34,211,238,.2);
            color: var(--accent); padding: 6px 16px; border-radius: 99px;
            font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase;
            font-family: 'Space Mono', monospace; margin-bottom: 28px;
        }
        .hero-badge .dot { width: 6px; height: 6px; border-radius: 50%; background: var(--safe); animation: pulse 2s infinite; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.3} }

        h1.hero-title {
            font-size: clamp(40px, 7vw, 80px);
            font-weight: 700; line-height: 1.05; color: #fff; margin-bottom: 20px;
        }
        h1.hero-title .accent { color: var(--accent); }
        .hero-sub {
            font-size: clamp(15px, 2.5vw, 20px); color: var(--muted);
            max-width: 560px; margin: 0 auto 40px; line-height: 1.6;
        }
        .hero-btns { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .btn-hero-primary {
            padding: 14px 32px; background: var(--accent); color: #000;
            border-radius: 10px; font-size: 15px; font-weight: 700;
            text-decoration: none; transition: all .2s; display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-hero-primary:hover { background: #67e8f9; box-shadow: 0 0 30px rgba(34,211,238,.35); transform: translateY(-2px); }
        .btn-hero-ghost {
            padding: 14px 32px; background: transparent; color: var(--text);
            border-radius: 10px; font-size: 15px; font-weight: 600;
            text-decoration: none; border: 1px solid var(--border);
            transition: all .2s; display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-hero-ghost:hover { border-color: var(--accent); color: var(--accent); }

        /* ── Stats strip ── */
        .stats-strip {
            position: relative; z-index: 10;
            border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);
            background: rgba(15,24,35,.6); backdrop-filter: blur(8px);
            padding: 28px 40px;
            display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 24px; text-align: center;
        }
        .stat-n { font-size: 32px; font-weight: 700; color: var(--accent); font-family: 'Space Mono', monospace; }
        .stat-l { font-size: 12px; color: var(--muted); margin-top: 4px; text-transform: uppercase; letter-spacing: .08em; font-family: 'Space Mono', monospace; }

        /* ── Features ── */
        .section { position: relative; z-index: 10; padding: 80px 40px; max-width: 1100px; margin: 0 auto; }
        .section-label { font-size: 11px; color: var(--accent); font-family: 'Space Mono', monospace; letter-spacing: .12em; text-transform: uppercase; margin-bottom: 14px; }
        .section-title { font-size: clamp(28px, 4vw, 42px); font-weight: 700; color: #fff; line-height: 1.15; margin-bottom: 16px; }
        .section-sub { font-size: 16px; color: var(--muted); max-width: 520px; line-height: 1.6; margin-bottom: 48px; }

        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
        .feature-card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 16px; padding: 28px;
            transition: border-color .2s, transform .2s;
        }
        .feature-card:hover { border-color: rgba(34,211,238,.3); transform: translateY(-4px); }
        .feature-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; margin-bottom: 18px;
        }
        .feature-icon.cyan   { background: rgba(34,211,238,.1);  color: var(--accent); }
        .feature-icon.green  { background: rgba(52,211,153,.1);  color: var(--safe); }
        .feature-icon.orange { background: rgba(251,191,36,.1);  color: var(--warn); }
        .feature-icon.red    { background: rgba(248,113,113,.1); color: var(--danger); }
        .feature-icon.purple { background: rgba(167,139,250,.1); color: #a78bfa; }
        .feature-icon.blue   { background: rgba(96,165,250,.1);  color: #60a5fa; }
        .feature-title { font-size: 16px; font-weight: 700; color: #fff; margin-bottom: 8px; }
        .feature-desc  { font-size: 13px; color: var(--muted); line-height: 1.6; }

        /* ── API preview ── */
        .api-section { position: relative; z-index: 10; padding: 0 40px 80px; max-width: 1100px; margin: 0 auto; }
        .code-block {
            background: var(--panel); border: 1px solid var(--border);
            border-radius: 14px; overflow: hidden;
        }
        .code-header {
            padding: 12px 18px; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 8px;
        }
        .code-dot { width: 10px; height: 10px; border-radius: 50%; }
        .code-title { font-size: 11px; color: var(--muted); font-family: 'Space Mono', monospace; margin-left: 6px; }
        .method-tag {
            font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 4px;
            font-family: 'Space Mono', monospace;
        }
        .method-post { background: rgba(34,211,238,.1); color: var(--accent); }
        .code-body { padding: 18px 20px; font-family: 'Space Mono', monospace; font-size: 12px; color: #94a3b8; line-height: 1.7; overflow-x: auto; }
        .code-key   { color: #7dd3fc; }
        .code-str   { color: #86efac; }
        .code-num   { color: #fca5a5; }
        .code-url   { color: var(--accent); }

        /* ── CTA ── */
        .cta-section {
            position: relative; z-index: 10;
            text-align: center; padding: 80px 40px 100px;
            border-top: 1px solid var(--border);
        }
        .cta-glow {
            position: absolute; top: 0; left: 50%; transform: translateX(-50%);
            width: 400px; height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent), transparent);
        }

        /* ── Footer ── */
        footer {
            position: relative; z-index: 10;
            padding: 24px 40px; border-top: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 12px;
        }
        footer p { font-size: 12px; color: var(--muted); font-family: 'Space Mono', monospace; }

        /* Animations */
        @keyframes fadeUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:none} }
        .fade-up { animation: fadeUp .6s ease-out forwards; opacity: 0; }
        .delay-1 { animation-delay: .1s; }
        .delay-2 { animation-delay: .2s; }
        .delay-3 { animation-delay: .3s; }
        .delay-4 { animation-delay: .4s; }

        @media (max-width: 640px) {
            nav { padding: 14px 20px; }
            .section, .api-section { padding-left: 20px; padding-right: 20px; }
            .stats-strip { padding: 20px; }
            .cta-section { padding: 60px 20px; }
        }
    </style>
</head>
<body>
<div class="grid-bg"></div>
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

<!-- Nav -->
<nav>
    <a class="nav-logo" href="/">
        <div class="nav-logo-icon"><i class="fas fa-shield-halved"></i></div>
        <span class="nav-logo-text">HomeGuard</span>
    </a>
    <div class="nav-links">
        @auth
            <a href="/dashboard" class="nav-btn nav-btn-primary">
                <i class="fas fa-gauge-high" style="margin-right:6px;"></i>Dashboard
            </a>
        @else
            <a href="/login"    class="nav-btn nav-btn-ghost">Sign In</a>
            <a href="/register" class="nav-btn nav-btn-primary">Get Started</a>
        @endauth
    </div>
</nav>

<!-- Hero -->
<section class="hero">
    <div>
        <div class="hero-badge fade-up"><span class="dot"></span> Live IoT Monitoring System</div>
        <h1 class="hero-title fade-up delay-1">
            Protect Your Home<br>With <span class="accent">Smart Sensors</span>
        </h1>
        <p class="hero-sub fade-up delay-2">
            Real-time temperature, humidity, gas, and motion monitoring powered by ESP32.
            Get instant alerts before small issues become big problems.
        </p>
        <div class="hero-btns fade-up delay-3">
            <a href="/register" class="btn-hero-primary">
                <i class="fas fa-rocket"></i> Start Monitoring
            </a>
            <a href="/login" class="btn-hero-ghost">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </a>
        </div>
    </div>
</section>

<!-- Stats -->
<div class="stats-strip">
    <div><div class="stat-n">24/7</div><div class="stat-l">Live Monitoring</div></div>
    <div><div class="stat-n">&lt;1s</div><div class="stat-l">Alert Latency</div></div>
    <div><div class="stat-n">3+</div><div class="stat-l">Sensor Types</div></div>
    <div><div class="stat-n">REST</div><div class="stat-l">API Ready</div></div>
</div>

<!-- Features -->
<section class="section">
    <div class="section-label">Capabilities</div>
    <h2 class="section-title">Everything you need to<br>keep your home safe</h2>
    <p class="section-sub">Built specifically for ESP32 prototypes. Just flash, connect, and monitor.</p>

    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon orange"><i class="fas fa-thermometer-half"></i></div>
            <div class="feature-title">Temperature & Humidity</div>
            <div class="feature-desc">DHT22 sensor readings logged every interval. Configurable warning and critical thresholds trigger instant alerts.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon red"><i class="fas fa-fire-flame-curved"></i></div>
            <div class="feature-title">Gas Detection</div>
            <div class="feature-desc">MQ-series sensor integration with PPM monitoring. Critical gas levels trigger immediate alerts before danger escalates.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon cyan"><i class="fas fa-camera"></i></div>
            <div class="feature-title">ESP32-CAM Images</div>
            <div class="feature-desc">Capture images on motion trigger or alert. Browse your full gallery with filters for motion, alerts, and favorites.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon green"><i class="fas fa-person-running"></i></div>
            <div class="feature-title">PIR Motion Detection</div>
            <div class="feature-desc">Motion events are logged and linked to camera captures. Know exactly when and where activity occurred.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon purple"><i class="fas fa-chart-line"></i></div>
            <div class="feature-title">Historical Charts</div>
            <div class="feature-desc">Visualise sensor trends over 24h, 7 days, or 30 days. Spot patterns and anomalies with interactive Chart.js graphs.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon blue"><i class="fas fa-plug"></i></div>
            <div class="feature-title">Simple REST API</div>
            <div class="feature-desc">Your ESP32 sends a single JSON POST. No authentication tokens needed — just your device_id and sensor values.</div>
        </div>
    </div>
</section>

<!-- API Preview -->
<section class="api-section">
    <div class="section-label" style="margin-bottom:14px;">Integration</div>
    <h2 class="section-title" style="font-size:clamp(22px,3vw,34px);margin-bottom:12px;">One POST. That's it.</h2>
    <p style="font-size:14px;color:var(--muted);margin-bottom:32px;max-width:480px;">Your ESP32 sends data to a single endpoint. HomeGuard handles storage, alerts, and visualisation automatically.</p>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:20px;">
        <div class="code-block">
            <div class="code-header">
                <div class="code-dot" style="background:#f87171;"></div>
                <div class="code-dot" style="background:#fbbf24;"></div>
                <div class="code-dot" style="background:#34d399;"></div>
                <span class="code-title">ESP32 Arduino Sketch</span>
            </div>
            <div class="code-body">
<span style="color:#64748b;">// Send sensor data</span>
HTTPClient http;
http.begin(<span class="code-str">"http://your-server/api/sensor-data"</span>);
http.addHeader(<span class="code-str">"Content-Type"</span>, <span class="code-str">"application/json"</span>);

String body = <span class="code-str">"{"</span>
  + <span class="code-str">"\"device_id\":\""</span> + WiFi.macAddress() + <span class="code-str">"\","</span>
  + <span class="code-str">"\"temperature\":"</span> + temp + <span class="code-str">","</span>
  + <span class="code-str">"\"humidity\":"</span>    + hum  + <span class="code-str">","</span>
  + <span class="code-str">"\"gas_level\":"</span>   + gas
  + <span class="code-str">"}"</span>;

int code = http.POST(body);
<span style="color:#64748b;">// 200 = success ✓</span>
            </div>
        </div>

        <div class="code-block">
            <div class="code-header">
                <div class="code-dot" style="background:#f87171;"></div>
                <div class="code-dot" style="background:#fbbf24;"></div>
                <div class="code-dot" style="background:#34d399;"></div>
                <span class="method-tag method-post" style="margin-left:6px;">POST</span>
                <span class="code-url">/api/sensor-data</span>
            </div>
            <div class="code-body">
{
  <span class="code-key">"device_id"</span>:    <span class="code-str">"AA:BB:CC:DD:EE:FF"</span>,
  <span class="code-key">"temperature"</span>:  <span class="code-num">28.5</span>,
  <span class="code-key">"humidity"</span>:     <span class="code-num">62.1</span>,
  <span class="code-key">"gas_level"</span>:    <span class="code-num">320</span>,
  <span class="code-key">"signal_strength"</span>: <span class="code-num">-65</span>
}

<span style="color:#64748b;">// Response</span>
{
  <span class="code-key">"success"</span>: <span style="color:#fca5a5;">true</span>,
  <span class="code-key">"reading_id"</span>: <span class="code-num">42</span>,
  <span class="code-key">"alerts_created"</span>: <span class="code-num">0</span>
}
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="cta-glow"></div>
    <div class="section-label" style="margin-bottom:16px;">Get Started</div>
    <h2 class="section-title" style="margin-bottom:14px;">Ready to protect your home?</h2>
    <p style="font-size:16px;color:var(--muted);margin-bottom:36px;">Create an account, register your ESP32, and start receiving live data in minutes.</p>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        <a href="/register" class="btn-hero-primary">
            <i class="fas fa-user-plus"></i> Create Free Account
        </a>
        <a href="/login" class="btn-hero-ghost">
            <i class="fas fa-sign-in-alt"></i> I have an account
        </a>
    </div>
</section>

<footer>
    <p>© {{ date('Y') }} HomeGuard · IoT Safety System</p>
    <p>Developed by: Eidref Jake S. Manalansan</p>
</footer>
</body>
</html>
