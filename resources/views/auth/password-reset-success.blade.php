<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="3;url={{ route('login') }}">
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
        .auth-card { position: relative; z-index: 1; width: min(570px, 100%); border: 1px solid rgba(255,255,255,.78); border-radius: 34px; padding: 58px 54px; background: linear-gradient(180deg, rgba(255,255,255,.92), rgba(235,249,255,.9)); box-shadow: 0 38px 95px rgba(10,31,68,.24); backdrop-filter: blur(16px); animation: reveal .55s cubic-bezier(.16, 1, .3, 1) both; }
        .steps { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 34px; }
        .step { height: 7px; border-radius: 999px; background: linear-gradient(135deg, #0284c7, #38bdf8); box-shadow: 0 0 0 4px rgba(56,189,248,.14); }
        h1 { margin: 0 0 38px; color: #07132d; font-family: var(--font-primary); font-size: clamp(36px, 7vw, 52px); line-height: 1; font-weight: 800; letter-spacing: 0; }
        .success { border-radius: 18px; padding: 18px 22px; color: #075985; background: rgba(224, 242, 254, .88); border: 1px solid rgba(125, 211, 252, .68); font-family: var(--font-secondary); font-size: 20px; line-height: 1.45; font-weight: 800; }
        .back { display: block; margin-top: 32px; color: #0369a1; text-align: center; text-decoration: none; font-family: var(--font-primary); font-size: 17px; font-weight: 800; }
        .back:hover { color: #0284c7; }
        .hint { margin-top: 12px; color: #64748b; text-align: center; font-family: var(--font-secondary); font-size: 13px; font-weight: 800; }
        @keyframes reveal { from { opacity: 0; transform: translateY(18px) scale(.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @media (max-width: 560px) { body { padding: 16px; } .auth-card { padding: 38px 28px; } .success { font-size: 16px; } }
    </style>
</head>
<body>
    <main class="auth-card">
        <div class="steps" aria-label="Password reset progress">
            <span class="step"></span>
            <span class="step"></span>
            <span class="step"></span>
            <span class="step"></span>
        </div>

        <h1>Forgot Password</h1>
        <div class="success">Password reset successful!</div>
        <a class="back" href="{{ route('login') }}">Back to Login</a>
        <div class="hint">Redirecting to login in 3 seconds...</div>
    </main>
</body>
</html>
