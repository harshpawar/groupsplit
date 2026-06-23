<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Group Split</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        :root {
            --bg: #0b0b0d;
            --bg-soft: #141418;
            --card: rgba(255,255,255,0.06);
            --border: rgba(255,255,255,0.12);
            --text: #f5f7fb;
            --muted: rgba(245,247,251,0.72);
            --red: #ff2f3e;
            --red-soft: rgba(255, 47, 62, 0.22);
            --shadow: 0 20px 60px rgba(0,0,0,0.45);
        }

        * {
            box-sizing: border-box;
        }

        html {
            color-scheme: dark;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top, rgba(255, 47, 62, 0.12), transparent 28%),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.06), transparent 18%),
                linear-gradient(160deg, var(--bg) 0%, #09090b 45%, var(--bg-soft) 100%);
            overflow: hidden;
        }

        .page {
            position: relative;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            isolation: isolate;
        }

        .ambient,
        .ambient::before,
        .ambient::after {
            position: absolute;
            inset: auto;
            border-radius: 999px;
            filter: blur(16px);
            pointer-events: none;
            content: "";
        }

        .ambient {
            width: 300px;
            height: 300px;
            background: rgba(255, 47, 62, 0.08);
            top: 8%;
            left: -60px;
            animation: drift 12s ease-in-out infinite;
        }

        .ambient::before {
            width: 180px;
            height: 180px;
            background: rgba(255,255,255,0.05);
            top: 380px;
            right: -40vw;
            animation: drift 16s ease-in-out infinite reverse;
        }

        .ambient::after {
            width: 220px;
            height: 220px;
            background: rgba(255, 47, 62, 0.06);
            bottom: -18vh;
            right: -20vw;
            animation: drift 14s ease-in-out infinite;
        }

        .shell {
            width: min(100%, 560px);
            text-align: center;
            padding: 48px 28px;
            border: 1px solid var(--border);
            border-radius: 28px;
            background: linear-gradient(180deg, rgba(255,255,255,0.08), rgba(255,255,255,0.03));
            backdrop-filter: blur(18px);
            box-shadow: var(--shadow);
            animation: fadeUp 900ms cubic-bezier(0.16, 1, 0.3, 1);
        }

        .logo-wrap {
            position: relative;
            width: 132px;
            margin: 0 auto 24px;
            animation: floatLogo 4.6s ease-in-out infinite;
        }

        .logo-wrap::before {
            content: "";
            position: absolute;
            inset: -18px;
            background: radial-gradient(circle, var(--red-soft) 0%, transparent 68%);
            z-index: -1;
            animation: pulseGlow 3.2s ease-in-out infinite;
        }

        .logo {
            width: 132px;
            height: 132px;
            object-fit: contain;
            border-radius: 22px;
            display: block;
            margin: 0 auto;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.35);
        }

        .brand {
            margin: 0;
            font-size: clamp(2.2rem, 5vw, 4rem);
            line-height: 0.95;
            font-weight: 800;
            letter-spacing: -0.05em;
            animation: fadeUp 1100ms cubic-bezier(0.16, 1, 0.3, 1);
        }

        .brand span {
            color: var(--red);
        }

        .actions {
            margin-top: 28px;
            display: flex;
            justify-content: center;
            gap: 14px;
            flex-wrap: wrap;
            animation: fadeUp 1250ms cubic-bezier(0.16, 1, 0.3, 1);
        }

        .btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 128px;
            padding: 12px 18px;
            border-radius: 999px;
            border: 1px solid transparent;
            color: var(--text);
            text-decoration: none;
            font-size: 0.96rem;
            font-weight: 600;
            transition: transform 220ms ease, border-color 220ms ease, background 220ms ease, box-shadow 220ms ease;
            overflow: hidden;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff4451 0%, #ff2f3e 100%);
            box-shadow: 0 12px 28px rgba(255, 47, 62, 0.28);
        }

        .btn-primary:hover {
            box-shadow: 0 16px 34px rgba(255, 47, 62, 0.38);
        }

        .btn-secondary {
            background: rgba(255,255,255,0.04);
            border-color: rgba(255,255,255,0.14);
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.08);
            border-color: rgba(255,255,255,0.22);
        }

        .btn::after {
            content: "";
            position: absolute;
            inset: 0;
            transform: translateX(-120%);
            background: linear-gradient(120deg, transparent 0%, rgba(255,255,255,0.24) 50%, transparent 100%);
            transition: transform 500ms ease;
        }

        .btn:hover::after {
            transform: translateX(120%);
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(28px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes floatLogo {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes pulseGlow {
            0%, 100% {
                opacity: 0.55;
                transform: scale(0.96);
            }
            50% {
                opacity: 1;
                transform: scale(1.06);
            }
        }

        @keyframes drift {
            0%, 100% {
                transform: translate3d(0, 0, 0);
            }
            50% {
                transform: translate3d(20px, -16px, 0);
            }
        }

        @media (max-width: 640px) {
            .shell {
                padding: 38px 20px;
                border-radius: 24px;
            }

            .logo,
            .logo-wrap {
                width: 108px;
                height: 108px;
            }

            .actions {
                gap: 12px;
            }

            .btn {
                width: 100%;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation: none !important;
                transition: none !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="ambient" aria-hidden="true"></div>

        <main class="shell">
            <div class="logo-wrap">
                <img src="{{ asset('logo-t.png') }}" alt="Group Split logo" class="logo">
            </div>

            <h1 class="brand">Group <span>Split</span></h1>

            @if (Route::has('login'))
                <nav class="actions">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-secondary">Register</a>
                        @endif
                    @endauth
                </nav>
            @endif
        </main>
    </div>
</body>
</html>