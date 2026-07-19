<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SalesPro</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        :root { --font-primary: "Plus Jakarta Sans", "Inter", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; --font-secondary: "Inter", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; color: #07132d; background: radial-gradient(circle at 18% 12%, rgba(56, 189, 248, .22), transparent 28%), linear-gradient(135deg, #8c98a7 0%, #b7c1cc 46%, #eef7ff 100%); font-family: var(--font-primary); overflow: hidden; }
        body::before { content: ""; position: fixed; inset: 0; background: linear-gradient(115deg, transparent 0 44%, rgba(255,255,255,.22) 44% 45%, transparent 45% 100%), repeating-linear-gradient(90deg, rgba(255,255,255,.16) 0 1px, transparent 1px 92px); opacity: .46; pointer-events: none; }
        .auth-card { position: relative; z-index: 1; width: min(570px, 100%); border: 1px solid rgba(255,255,255,.78); border-radius: 34px; padding: 52px 54px; background: linear-gradient(180deg, rgba(255,255,255,.92), rgba(235,249,255,.9)); box-shadow: 0 38px 95px rgba(10,31,68,.24); backdrop-filter: blur(16px); animation: reveal .55s cubic-bezier(.16, 1, .3, 1) both; }
        .steps { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 34px; }
        .step { height: 7px; border-radius: 999px; background: rgba(148,163,184,.28); }
        .step.active { background: linear-gradient(135deg, #0284c7, #38bdf8); box-shadow: 0 0 0 4px rgba(56,189,248,.14); }
        h1 { margin: 0; color: #07132d; font-family: var(--font-primary); font-size: clamp(36px, 7vw, 52px); line-height: 1; font-weight: 800; letter-spacing: 0; }
        p { margin: 16px 0 28px; color: #64748b; font-family: var(--font-secondary); font-size: 20px; line-height: 1.45; font-weight: 700; }
        .alert { margin-bottom: 16px; border-radius: 18px; padding: 14px 18px; font-family: var(--font-secondary); font-size: 16px; line-height: 1.55; font-weight: 800; }
        .success { color: #075985; background: rgba(224, 242, 254, .88); border: 1px solid rgba(125, 211, 252, .68); }
        .info { color: #0f3a5f; background: rgba(239, 246, 255, .9); border: 1px solid rgba(191, 219, 254, .8); }
        input { width: 100%; height: 62px; border: 1px solid rgba(148,163,184,.32); border-radius: 999px; padding: 0 22px; outline: none; color: #0f172a; background: rgba(255,255,255,.92); font-family: var(--font-secondary); font-size: 19px; font-weight: 800; letter-spacing: .08em; }
        input::placeholder { color: #94a3b8; letter-spacing: 0; }
        input:focus { border-color: #38bdf8; box-shadow: 0 0 0 4px rgba(56,189,248,.15); }
        .primary { width: 100%; min-height: 60px; margin-top: 26px; border: 0; border-radius: 999px; color: #fff; background: linear-gradient(135deg, #0284c7, #38bdf8); font-family: var(--font-primary); font-size: 20px; font-weight: 800; cursor: pointer; box-shadow: 0 18px 34px rgba(2,132,199,.28); transition: transform .16s ease, box-shadow .16s ease; }
        .primary:hover { transform: translateY(-1px); box-shadow: 0 22px 40px rgba(2,132,199,.32); }
        .back { display: block; margin-top: 30px; color: #0369a1; text-align: center; text-decoration: none; font-family: var(--font-primary); font-size: 17px; font-weight: 800; }
        .back:hover { color: #0284c7; }
        .error { margin-top: 12px; color: #b91c1c; font-family: var(--font-secondary); font-size: 14px; font-weight: 800; }
        @keyframes reveal { from { opacity: 0; transform: translateY(18px) scale(.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @media (max-width: 560px) { body { padding: 16px; } .auth-card { padding: 36px 28px; } input, .primary { height: 56px; min-height: 56px; font-size: 16px; } p, .alert { font-size: 15px; } }
    </style>
</head>
<body>
    <main class="auth-card">
        <div class="steps" aria-label="Password reset progress">
            <span class="step active"></span>
            <span class="step active"></span>
            <span class="step"></span>
            <span class="step"></span>
        </div>

        <h1>Forgot Password</h1>
        <p>Enter the OTP sent to your email</p>

        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        <div class="alert info">OTP sent to your email. Please check your inbox and spam folder.</div>

        <form method="POST" action="{{ route('password.otp.verify') }}">
            @csrf
            <input type="hidden" name="email" value="{{ old('email', $email) }}">
            <input type="text" name="otp" value="{{ old('otp') }}" placeholder="Enter OTP" inputmode="numeric" maxlength="6" autocomplete="one-time-code" required autofocus>
            @error('otp')
                <div class="error">{{ $message }}</div>
            @enderror
            <button class="primary" type="submit">Verify OTP</button>
        </form>

        <a class="back" href="{{ route('login') }}">Back to Login</a>
    </main>
</body>
</html>
