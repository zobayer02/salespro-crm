<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SalesPro</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Manrope", "Inter", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #07132d;
            background:
                radial-gradient(circle at 18% 12%, rgba(56, 189, 248, .22), transparent 28%),
                linear-gradient(135deg, #8c98a7 0%, #b7c1cc 46%, #eef7ff 100%);
            display: grid;
            place-items: center;
            padding: 32px;
        }

        .auth-shell {
            width: min(1120px, 100%);
            min-height: 680px;
            display: grid;
            grid-template-columns: .92fr 1.18fr;
            border-radius: 34px;
            overflow: hidden;
            background: rgba(255, 255, 255, .82);
            box-shadow: 0 38px 95px rgba(10, 31, 68, .24);
            border: 1px solid rgba(255, 255, 255, .75);
            animation: shellReveal .72s cubic-bezier(.16, 1, .3, 1) both;
        }

        .form-panel {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 58px 72px;
            background:
                radial-gradient(circle at 22% 88%, rgba(125, 211, 252, .42), transparent 34%),
                linear-gradient(180deg, rgba(255, 255, 255, .92), rgba(235, 249, 255, .9));
        }

        .brand-pill {
            position: absolute;
            top: 34px;
            left: 38px;
            padding: 10px 18px;
            border: 1px solid #cbd5e1;
            border-radius: 999px;
            font-family: "Manrope", "Inter", ui-sans-serif, system-ui, sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: #1e3a8a;
            background: rgba(255, 255, 255, .82);
            animation: slideFromTop .68s cubic-bezier(.16, 1, .3, 1) .12s both;
        }

        .form-card {
            width: min(340px, 100%);
            margin: 0 auto;
        }

        .form-card > * {
            animation: slideFromLeft .68s cubic-bezier(.16, 1, .3, 1) both;
        }

        .form-card h1 { animation-delay: .18s; }
        .form-card .subtitle { animation-delay: .25s; }
        .form-card .error { animation-delay: .28s; }
        .form-card .field:nth-of-type(1) { animation-delay: .32s; }
        .form-card .field:nth-of-type(2) { animation-delay: .4s; }
        .form-card .row { animation-delay: .48s; }
        .form-card .submit { animation-delay: .56s; }

        h1 {
            margin: 0;
            font-family: "Manrope", "Inter", ui-sans-serif, system-ui, sans-serif;
            font-size: 31px;
            line-height: 1.1;
            letter-spacing: 0;
        }

        .subtitle {
            margin: 10px 0 34px;
            color: #64748b;
            font-size: 14px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-family: "Manrope", "Inter", ui-sans-serif, system-ui, sans-serif;
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
        }

        .field {
            margin-bottom: 18px;
        }

        input {
            width: 100%;
            height: 48px;
            border: 1px solid rgba(148, 163, 184, .32);
            border-radius: 999px;
            padding: 0 20px;
            outline: none;
            color: #0f172a;
            background: rgba(255, 255, 255, .9);
            transition: border-color .2s, box-shadow .2s;
        }

        input:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, .15);
        }

        .password-wrap {
            position: relative;
        }

        .password-wrap input {
            padding-right: 54px;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 16px;
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            transform: translateY(-50%);
            border: 0;
            border-radius: 50%;
            color: #64748b;
            background: transparent;
            cursor: pointer;
        }

        .password-toggle:hover {
            color: #0284c7;
            background: rgba(56, 189, 248, .1);
        }

        .password-toggle svg {
            width: 20px;
            height: 20px;
        }

        .password-toggle .hidden {
            display: none;
        }

        .row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin: 4px 0 24px;
            color: #64748b;
            font-size: 13px;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .remember input {
            width: 16px;
            height: 16px;
            padding: 0;
            accent-color: #0284c7;
        }

        .link {
            color: #0369a1;
            text-decoration: none;
            font-weight: 700;
        }

        .submit {
            width: 100%;
            height: 52px;
            border: 0;
            border-radius: 999px;
            font-family: "Manrope", "Inter", ui-sans-serif, system-ui, sans-serif;
            color: #fff;
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 18px 34px rgba(2, 132, 199, .28);
            transition: transform .2s, box-shadow .2s;
        }

        .submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 22px 40px rgba(2, 132, 199, .32);
        }

        .error,
        .success {
            margin: 0 0 18px;
            padding: 12px 14px;
            border-radius: 16px;
            font-size: 13px;
            font-weight: 700;
        }

        .error {
            color: #991b1b;
            background: #fee2e2;
        }

        .success {
            color: #047857;
            background: #dcfce7;
            border: 1px solid #bbf7d0;
        }

        .visual-panel {
            position: relative;
            min-height: 680px;
            overflow: hidden;
            background:
                linear-gradient(135deg, rgba(8, 47, 73, .35), rgba(14, 165, 233, .28)),
                radial-gradient(circle at 18% 22%, rgba(255, 255, 255, .88), transparent 9%),
                linear-gradient(145deg, #0f172a, #0ea5e9 58%, #bae6fd);
            animation: slideFromRight .82s cubic-bezier(.16, 1, .3, 1) .08s both;
        }

        .visual-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(115deg, transparent 0 22%, rgba(255, 255, 255, .16) 22% 23%, transparent 23% 100%),
                repeating-linear-gradient(90deg, rgba(255,255,255,.08) 0 1px, transparent 1px 78px);
            opacity: .75;
        }

        .floating-card {
            position: absolute;
            z-index: 2;
            border-radius: 18px;
            background: rgba(255, 255, 255, .92);
            box-shadow: 0 22px 45px rgba(15, 23, 42, .18);
            backdrop-filter: blur(12px);
        }

        .task-card {
            top: 72px;
            left: 70px;
            width: 235px;
            padding: 18px 20px;
            animation:
                slideFromTop .68s cubic-bezier(.16, 1, .3, 1) .34s both,
                gentleFloatY 4.8s ease-in-out 1.1s infinite;
        }

        .task-card strong,
        .meeting-card strong {
            display: block;
            color: #082f49;
            font-size: 14px;
            margin-bottom: 6px;
        }

        .task-card span,
        .meeting-card span {
            color: #64748b;
            font-size: 12px;
        }

        .stats-card {
            right: 74px;
            bottom: 84px;
            width: 270px;
            padding: 22px;
            animation:
                slideFromRight .68s cubic-bezier(.16, 1, .3, 1) .5s both,
                gentleFloatDiagonal 5.4s ease-in-out 1.2s infinite;
        }

        .stats-card h2 {
            margin: 0 0 16px;
            font-size: 20px;
        }

        .bar {
            height: 12px;
            border-radius: 999px;
            margin: 12px 0;
            background: rgba(2, 132, 199, .12);
            overflow: hidden;
        }

        .bar span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #0284c7, #7dd3fc);
            animation: barPulse 2.6s ease-in-out infinite;
        }

        .meeting-card {
            left: 92px;
            bottom: 102px;
            width: 230px;
            padding: 18px;
            animation:
                slideFromLeft .68s cubic-bezier(.16, 1, .3, 1) .42s both,
                gentleFloatReverse 5.2s ease-in-out 1.14s infinite;
        }

        .calendar-strip {
            position: absolute;
            left: 96px;
            right: 96px;
            bottom: 214px;
            z-index: 2;
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            padding: 14px;
            border-radius: 22px;
            color: #e0f2fe;
            background: rgba(7, 19, 45, .36);
            backdrop-filter: blur(14px);
            animation: slideFromBottom .78s cubic-bezier(.16, 1, .3, 1) .6s both;
        }

        .day {
            text-align: center;
            font-size: 12px;
        }

        .day strong {
            display: block;
            margin-top: 6px;
            color: #fff;
            font-size: 18px;
        }

        .hero-copy {
            position: absolute;
            left: 70px;
            right: 70px;
            top: 185px;
            z-index: 1;
            color: white;
            animation: slideFromBottom .78s cubic-bezier(.16, 1, .3, 1) .38s both;
        }

        .hero-copy h2 {
            width: min(470px, 100%);
            margin: 0 0 16px;
            font-family: "Manrope", "Inter", ui-sans-serif, system-ui, sans-serif;
            font-size: 54px;
            line-height: 1;
        }

        .hero-copy p {
            width: min(390px, 100%);
            margin: 0;
            color: #dff6ff;
            font-size: 17px;
            line-height: 1.6;
        }

        @keyframes shellReveal {
            from {
                opacity: 0;
                transform: translateY(18px) scale(.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes slideFromLeft {
            from {
                opacity: 0;
                transform: translateX(-34px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideFromRight {
            from {
                opacity: 0;
                transform: translateX(36px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideFromTop {
            from {
                opacity: 0;
                transform: translateY(-24px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideFromBottom {
            from {
                opacity: 0;
                transform: translateY(34px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes gentleFloatY {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(10px);
            }
        }

        @keyframes gentleFloatDiagonal {
            0%, 100% {
                transform: translate(0, 0);
            }
            50% {
                transform: translate(-8px, 8px);
            }
        }

        @keyframes gentleFloatReverse {
            0%, 100% {
                transform: translate(0, 0);
            }
            50% {
                transform: translate(8px, -8px);
            }
        }

        @keyframes barPulse {
            0%, 100% {
                filter: brightness(1);
            }
            50% {
                filter: brightness(1.12);
            }
        }

        @media (max-width: 900px) {
            body {
                padding: 18px;
            }

            .auth-shell {
                grid-template-columns: 1fr;
            }

            .form-panel {
                min-height: 650px;
                padding: 92px 28px 36px;
            }

            .visual-panel {
                display: none;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>
<body>
    <main class="auth-shell">
        <section class="form-panel">
            <div class="brand-pill">SalesPro</div>

            <form class="form-card" method="POST" action="{{ route('login.store') }}">
                @csrf

                <h1>Owner Login</h1>
                <p class="subtitle">Access your sales, inventory and CRM dashboard</p>

                @if ($errors->any())
                    <div class="error">{{ $errors->first() }}</div>
                @endif

                @if (session('success'))
                    <div class="success">{{ session('success') }}</div>
                @endif

                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <div class="password-wrap">
                        <input id="password" type="password" name="password" autocomplete="current-password" required>
                        <button class="password-toggle" type="button" aria-label="Show password" aria-controls="password">
                            <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg class="eye-closed hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="m3 3 18 18"/>
                                <path d="M10.6 10.6A3 3 0 0 0 12 15a3 3 0 0 0 2.4-4.8"/>
                                <path d="M9.9 4.3A10 10 0 0 1 12 4.1c6.5 0 10 7 10 7a17.9 17.9 0 0 1-3.1 4.2"/>
                                <path d="M6.6 6.7A17.7 17.7 0 0 0 2 12s3.5 7 10 7a9.7 9.7 0 0 0 4.2-.9"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="row">
                    <label class="remember">
                        <input type="checkbox" name="remember" value="1">
                        Remember me
                    </label>
                    <a class="link" href="{{ route('password.request') }}">Forgot password?</a>
                </div>

                <button class="submit" type="submit">Login</button>

            </form>
        </section>

        <section class="visual-panel" aria-hidden="true">
            <div class="floating-card task-card">
                <strong>Inventory Review</strong>
                <span>Stock, orders and CRM follow-ups</span>
            </div>
            <div class="hero-copy">
                <h2>Sales intelligence in one place</h2>
                <p>Track products, customers, orders and employee KPI from a clean owner dashboard.</p>
            </div>
            <div class="calendar-strip">
                <div class="day">Sun<strong>22</strong></div>
                <div class="day">Mon<strong>23</strong></div>
                <div class="day">Tue<strong>24</strong></div>
                <div class="day">Wed<strong>25</strong></div>
                <div class="day">Thu<strong>26</strong></div>
                <div class="day">Fri<strong>27</strong></div>
                <div class="day">Sat<strong>28</strong></div>
            </div>
            <div class="floating-card meeting-card">
                <strong>Follow-up Queue</strong>
                <span>12 inactive customers assigned</span>
            </div>
            <div class="floating-card stats-card">
                <h2>Today's Flow</h2>
                <div class="bar"><span style="width: 86%"></span></div>
                <div class="bar"><span style="width: 64%"></span></div>
                <div class="bar"><span style="width: 72%"></span></div>
            </div>
        </section>
    </main>

    <script>
        const passwordInput = document.querySelector('#password');
        const passwordToggle = document.querySelector('.password-toggle');
        const eyeOpen = document.querySelector('.eye-open');
        const eyeClosed = document.querySelector('.eye-closed');

        passwordToggle.addEventListener('click', () => {
            const shouldShow = passwordInput.type === 'password';

            passwordInput.type = shouldShow ? 'text' : 'password';
            passwordToggle.setAttribute('aria-label', shouldShow ? 'Hide password' : 'Show password');
            eyeOpen.classList.toggle('hidden', shouldShow);
            eyeClosed.classList.toggle('hidden', ! shouldShow);
        });
    </script>
</body>
</html>
