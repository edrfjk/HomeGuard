<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — HomeGuard</title>
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --accent: #22d3ee; --accent-dim: #0e7490;
            --bg: #080d14; --panel: #0f1823; --card: #131f2e;
            --border: rgba(34,211,238,0.12); --text: #e2e8f0; --muted: #64748b;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg); color: var(--text);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            padding: 20px; position: relative; overflow: hidden;
        }
        /* Grid background */
        body::before {
            content: '';
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(34,211,238,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(34,211,238,.04) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
        }
        /* Glowing orb */
        body::after {
            content: '';
            position: fixed; top: -200px; left: 50%; transform: translateX(-50%);
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(34,211,238,.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .login-card {
            width: 100%; max-width: 400px;
            background: var(--card); border: 1px solid var(--border);
            border-radius: 18px; padding: 36px 32px;
            position: relative; z-index: 10;
            animation: slideUp 0.5s cubic-bezier(.4,0,.2,1) forwards;
        }
        @keyframes slideUp { from{opacity:0;transform:translateY(24px);} to{opacity:1;transform:none;} }

        .logo { text-align: center; margin-bottom: 32px; }
        .logo-icon {
            width: 56px; height: 56px; margin: 0 auto 14px;
            background: linear-gradient(135deg, var(--accent), var(--accent-dim));
            border-radius: 14px; display: flex; align-items: center; justify-content: center;
            font-size: 24px; color: #000;
            box-shadow: 0 0 40px rgba(34,211,238,.25);
        }
        .logo h1 { font-size: 24px; font-weight: 700; color: #fff; }
        .logo p  { font-size: 11px; color: var(--accent); font-family: 'Space Mono', monospace; letter-spacing: 0.12em; text-transform: uppercase; margin-top: 4px; }

        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block; font-size: 11px; font-weight: 600; color: var(--muted);
            margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.08em;
            font-family: 'Space Mono', monospace;
        }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
            color: var(--muted); font-size: 13px; pointer-events: none;
        }
        .form-input {
            width: 100%; padding: 11px 14px 11px 38px;
            background: rgba(255,255,255,0.04); border: 1px solid var(--border);
            border-radius: 9px; color: var(--text); font-size: 14px;
            font-family: 'DM Sans', sans-serif; transition: border-color .2s, background .2s;
        }
        .form-input:focus { outline: none; border-color: var(--accent); background: rgba(34,211,238,.04); }
        .form-input::placeholder { color: rgba(100,116,139,.5); }

        .remember-row {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 20px; font-size: 13px; color: var(--muted);
        }
        .remember-row input { accent-color: var(--accent); }

        .btn-login {
            width: 100%; padding: 12px;
            background: var(--accent); color: #000;
            border: none; border-radius: 9px;
            font-size: 14px; font-weight: 700; cursor: pointer;
            transition: all .2s; letter-spacing: 0.03em;
            font-family: 'DM Sans', sans-serif;
        }
        .btn-login:hover { background: #67e8f9; box-shadow: 0 0 20px rgba(34,211,238,.3); }

        .divider { text-align: center; margin: 20px 0; color: var(--muted); font-size: 12px; position: relative; }
        .divider::before { content: ''; position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: var(--border); }
        .divider span { background: var(--card); padding: 0 12px; position: relative; }

        .register-link {
            text-align: center; font-size: 13px; color: var(--muted);
        }
        .register-link a { color: var(--accent); font-weight: 600; text-decoration: none; }
        .register-link a:hover { text-decoration: underline; }

        .error-box {
            margin-bottom: 16px; padding: 12px 14px;
            background: rgba(248,113,113,.08); border: 1px solid rgba(248,113,113,.2);
            border-radius: 8px; font-size: 13px; color: #f87171;
        }

        .hint-box {
            margin-top: 16px; padding: 12px 14px;
            background: rgba(34,211,238,.05); border: 1px solid rgba(34,211,238,.1);
            border-radius: 8px; font-size: 11px; color: var(--muted);
            font-family: 'Space Mono', monospace; line-height: 1.7;
        }
        .hint-box .hint-title { color: var(--accent); font-weight: 700; margin-bottom: 6px; font-size: 10px; letter-spacing: 0.1em; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">
            <div class="logo-icon"><i class="fas fa-shield-halved"></i></div>
            <h1>HomeGuard</h1>
            <p>IoT Safety System</p>
        </div>

        @if($errors->any())
            <div class="error-box">
                @foreach($errors->all() as $e)
                    <div><i class="fas fa-exclamation-circle" style="margin-right:6px;"></i>{{ $e }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-wrap">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                </div>
            </div>

            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me</label>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-right-to-bracket" style="margin-right:8px;"></i>Sign In
            </button>
        </form>

        <div class="divider"><span>or</span></div>

        <div class="register-link">
            Don't have an account? <a href="/register">Create one</a>
        </div>

        <div class="hint-box">
            <div class="hint-title">— DEV CREDENTIALS —</div>
            Email: test@example.com<br>
            Password: 12345678
        </div>
    </div>
</body>
</html>
