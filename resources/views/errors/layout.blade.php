@php
    $code = $code ?? '500';
    $title = $title ?? 'Terjadi Kesalahan';
    $message = $message ?? 'Sistem sedang mengalami kendala. Silakan coba kembali beberapa saat lagi.';
    $description = $description ?? 'Jika masalah masih terjadi, hubungi pengelola sistem SIAMI.';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} - {{ config('app.name', 'SIAMI') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo-pnc-1.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/logo-pnc-1.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo-pnc-1.png') }}">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #0f172a;
            background: #f8fafc;
        }

        .page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(0, 1fr);
        }

        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px;
            overflow: hidden;
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: linear-gradient(135deg, rgba(15, 23, 42, .84), rgba(30, 64, 175, .74)), url("{{ asset('img/pnc.png') }}");
            background-size: cover;
            background-position: center;
        }

        .panel {
            position: relative;
            width: min(100%, 720px);
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, .28);
            border-radius: 16px;
            background: rgba(255, 255, 255, .94);
            box-shadow: 0 24px 70px rgba(15, 23, 42, .24);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 28px;
        }

        .brand img {
            width: 52px;
            height: 52px;
            object-fit: contain;
        }

        .brand-title {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            color: #2563eb;
        }

        .brand-subtitle {
            margin: 2px 0 0;
            font-size: 13px;
            color: #64748b;
        }

        .code {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 84px;
            height: 44px;
            padding: 0 18px;
            border-radius: 999px;
            background: #dbeafe;
            color: #1d4ed8;
            font-weight: 800;
            letter-spacing: .04em;
        }

        h1 {
            margin: 22px 0 12px;
            font-size: clamp(28px, 5vw, 44px);
            line-height: 1.12;
        }

        .message {
            margin: 0;
            max-width: 580px;
            color: #475569;
            font-size: 17px;
            line-height: 1.7;
        }

        .description {
            margin: 14px 0 0;
            color: #64748b;
            font-size: 14px;
            line-height: 1.65;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 30px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 0 18px;
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            color: #334155;
            text-decoration: none;
            font-weight: 700;
            background: #ffffff;
            cursor: pointer;
        }

        .button-primary {
            border-color: #2563eb;
            background: #2563eb;
            color: #ffffff;
        }

        .button:hover {
            filter: brightness(.96);
        }

        @media (max-width: 640px) {
            .hero {
                padding: 20px;
            }

            .panel {
                padding: 28px;
            }

            .actions {
                flex-direction: column;
            }

            .button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="hero">
            <div class="panel">
                <div class="brand">
                    <img src="{{ asset('img/logo-pnc-1.png') }}" alt="Logo PNC">
                    <div>
                        <p class="brand-title">SIAMI</p>
                        <p class="brand-subtitle">Sistem Informasi Audit Mutu Internal</p>
                    </div>
                </div>

                <span class="code">ERROR {{ $code }}</span>
                <h1>{{ $title }}</h1>
                <p class="message">{{ $message }}</p>
                <p class="description">{{ $description }}</p>

                <div class="actions">
                    <a class="button button-primary" href="{{ url('/') }}">Ke Beranda</a>
                    <button class="button" type="button" onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ url('/') }}'">
                        Kembali
                    </button>
                    @if (Route::has('login'))
                        <a class="button" href="{{ route('login') }}">Login</a>
                    @endif
                </div>
            </div>
        </section>
    </main>
</body>
</html>
