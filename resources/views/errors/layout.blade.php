@php
    $isDashboard = request()->is('dashboard*');
    $statusCode = $statusCode ?? 500;
    $title = $title ?? 'Something went wrong';
    $message = $message ?? 'Please try again in a moment.';
    $hint = $hint ?? null;
    $primaryLabel = $primaryLabel ?? ($isDashboard ? 'Go to Dashboard' : 'Go Home');
    $primaryUrl = $primaryUrl ?? ($isDashboard && auth()->check() ? route('dashboard') : route('home'));
    $secondaryLabel = $secondaryLabel ?? ($isDashboard ? 'Go Home' : 'Contact Us');
    $secondaryUrl = $secondaryUrl ?? ($isDashboard ? route('home') : route('contact'));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $statusCode }} | {{ $title }}</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/favicons/apple-touch-icon.png') }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicons/favicon-32x32.png') }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicons/favicon-16x16.png') }}" />
    @if ($isDashboard)
        <link rel="stylesheet" href="{{ asset('assets/dashboard/css/bootstrap.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/dashboard/css/plugins.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/dashboard/css/kaiadmin.min.css') }}" />
    @endif
    <style>
        :root {
            --error-primary: #24126a;
            --error-accent: #ef2b2d;
            --error-ink: #1f2937;
            --error-muted: #6b7280;
            --error-line: rgba(148, 163, 184, 0.24);
            --error-surface: rgba(255, 255, 255, 0.92);
            --error-soft: rgba(255, 255, 255, 0.72);
            --error-glow: rgba(36, 18, 106, 0.12);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Public Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--error-ink);
            background:
                radial-gradient(circle at top left, rgba(239, 43, 45, 0.08), transparent 28%),
                radial-gradient(circle at right center, rgba(36, 18, 106, 0.12), transparent 34%),
                linear-gradient(180deg, #f8fafc 0%, #eef3f9 100%);
        }

        .error-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 20px;
        }

        .error-card {
            width: min(100%, 1080px);
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            gap: 0;
            overflow: hidden;
            border-radius: 32px;
            border: 1px solid var(--error-line);
            background: var(--error-surface);
            box-shadow: 0 28px 80px rgba(15, 23, 42, 0.14);
            backdrop-filter: blur(14px);
        }

        .error-panel {
            padding: 48px;
            position: relative;
        }

        .error-panel--hero {
            background:
                radial-gradient(circle at top right, rgba(255,255,255,0.28), transparent 28%),
                linear-gradient(160deg, var(--error-primary) 0%, #33208b 50%, #1e0f58 100%);
            color: #fff;
        }

        .error-panel--hero::after {
            content: "";
            position: absolute;
            inset: auto 32px 24px auto;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            filter: blur(2px);
        }

        .error-context {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            border-radius: 999px;
            background: rgba(255,255,255,0.12);
            color: rgba(255,255,255,0.92);
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .error-context::before {
            content: "";
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: #22c55e;
            box-shadow: 0 0 0 6px rgba(34, 197, 94, 0.18);
        }

        .error-code {
            margin: 30px 0 10px;
            font-size: clamp(4.8rem, 10vw, 7.5rem);
            line-height: 0.95;
            font-weight: 900;
            letter-spacing: -0.06em;
        }

        .error-hero-title {
            margin: 0 0 14px;
            font-size: clamp(1.8rem, 3vw, 2.9rem);
            line-height: 1.08;
            font-weight: 800;
            max-width: 12ch;
        }

        .error-hero-copy {
            margin: 0;
            max-width: 34rem;
            color: rgba(255,255,255,0.82);
            font-size: 1rem;
            line-height: 1.75;
        }

        .error-badge-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }

        .error-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,0.12);
            color: rgba(255,255,255,0.92);
            font-size: 0.9rem;
            font-weight: 600;
        }

        .error-panel--content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            background:
                radial-gradient(circle at top left, rgba(36, 18, 106, 0.06), transparent 22%),
                linear-gradient(180deg, #ffffff 0%, #f9fbff 100%);
        }

        .error-logo {
            width: 86px;
            height: 86px;
            border-radius: 24px;
            padding: 12px;
            background: #fff;
            box-shadow: 0 18px 40px var(--error-glow);
            border: 1px solid rgba(36, 18, 106, 0.08);
        }

        .error-kicker {
            display: inline-block;
            margin: 22px 0 12px;
            color: var(--error-primary);
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .error-title {
            margin: 0 0 14px;
            font-size: clamp(1.6rem, 2.5vw, 2.5rem);
            line-height: 1.12;
            font-weight: 800;
            color: var(--error-ink);
        }

        .error-message {
            margin: 0;
            color: var(--error-muted);
            font-size: 1rem;
            line-height: 1.8;
            max-width: 34rem;
        }

        .error-hint {
            margin-top: 18px;
            padding: 16px 18px;
            border-radius: 18px;
            background: linear-gradient(180deg, #fbfdff 0%, #f4f8ff 100%);
            border: 1px solid rgba(36, 18, 106, 0.08);
            color: #475569;
            line-height: 1.65;
        }

        .error-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 28px;
        }

        .error-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 50px;
            padding: 0 22px;
            border-radius: 16px;
            text-decoration: none;
            font-weight: 700;
            transition: transform 0.18s ease, box-shadow 0.18s ease, opacity 0.18s ease;
        }

        .error-btn:hover {
            transform: translateY(-1px);
            opacity: 0.96;
        }

        .error-btn--primary {
            color: #fff;
            background: linear-gradient(135deg, var(--error-accent) 0%, #d61d1f 100%);
            box-shadow: 0 18px 30px rgba(239, 43, 45, 0.22);
        }

        .error-btn--secondary {
            color: var(--error-primary);
            background: #fff;
            border: 1px solid rgba(36, 18, 106, 0.12);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.07);
        }

        .error-footer {
            margin-top: 28px;
            color: #94a3b8;
            font-size: 0.9rem;
        }

        @media (max-width: 991.98px) {
            .error-card {
                grid-template-columns: 1fr;
            }

            .error-panel {
                padding: 32px 24px;
            }

            .error-hero-title {
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <main class="error-shell">
        <section class="error-card" aria-labelledby="error-title">
            <div class="error-panel error-panel--hero">
                <span class="error-context">{{ $isDashboard ? 'Dashboard Context' : 'Public Website' }}</span>
                <div class="error-code">{{ $statusCode }}</div>
                <h1 class="error-hero-title">{{ $isDashboard ? 'We hit a pause in the dashboard.' : 'We could not reach that page.' }}</h1>
                <p class="error-hero-copy">
                    {{ $isDashboard
                        ? 'Your current church admin task is safe. We just need to guide you back to the right place so you can keep moving.'
                        : 'Let us help you get back to worship resources, church information, and the next clear step on the site.' }}
                </p>
                <div class="error-badge-row">
                    <span class="error-badge">RCCG Goals Parish</span>
                    <span class="error-badge">{{ $isDashboard ? 'Admin Experience' : 'Guest Experience' }}</span>
                </div>
            </div>

            <div class="error-panel error-panel--content">
                <img class="error-logo" src="{{ asset('assets/images/resources/goals_logo_real.png') }}" alt="RCCG Goals Parish logo">
                <span class="error-kicker">Helpful Next Step</span>
                <h2 id="error-title" class="error-title">{{ $title }}</h2>
                <p class="error-message">{{ $message }}</p>
                @if ($hint)
                    <div class="error-hint">{{ $hint }}</div>
                @endif
                <div class="error-actions">
                    <a class="error-btn error-btn--primary" href="{{ $primaryUrl }}">{{ $primaryLabel }}</a>
                    <a class="error-btn error-btn--secondary" href="{{ $secondaryUrl }}">{{ $secondaryLabel }}</a>
                </div>
                <div class="error-footer">
                    {{ $isDashboard
                        ? 'If this keeps happening, return to the dashboard and try the action again.'
                        : 'If this keeps happening, feel free to reach out through the church contact page.' }}
                </div>
            </div>
        </section>
    </main>
</body>
</html>
