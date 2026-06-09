<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'VYD Abogados' }}</title>
    @isset($description)
        <meta name="description" content="{{ $description }}">
    @endisset
    <style>
        :root {
            color-scheme: light;
            --bg: #f7f4ef;
            --ink: #191714;
            --muted: #6f6a61;
            --brand: #8a5a26;
            --brand-dark: #4d3420;
            --line: #e2ddd4;
            --surface: #fffdfa;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            line-height: 1.6;
        }

        a {
            color: inherit;
        }

        .site-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .site-header {
            border-bottom: 1px solid var(--line);
            background: rgba(255, 253, 250, 0.92);
            backdrop-filter: blur(16px);
            position: sticky;
            top: 0;
            z-index: 20;
        }

        .site-header__inner,
        .section {
            width: min(1120px, calc(100% - 32px));
            margin: 0 auto;
        }

        .site-header__inner {
            min-height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }

        .brand {
            text-decoration: none;
            font-weight: 800;
            letter-spacing: 0;
        }

        .brand span {
            display: block;
            color: var(--muted);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .nav {
            display: flex;
            align-items: center;
            gap: 18px;
            color: var(--muted);
            font-size: 14px;
            font-weight: 700;
        }

        .nav a {
            text-decoration: none;
        }

        .hero {
            padding: 82px 0 56px;
            background:
                linear-gradient(120deg, rgba(77, 52, 32, 0.92), rgba(25, 23, 20, 0.76)),
                url("https://images.unsplash.com/photo-1589829545856-d10d557cf95f?auto=format&fit=crop&w=1800&q=80") center/cover;
            color: #fffdfa;
        }

        .hero h1,
        .content h1 {
            max-width: 820px;
            margin: 0;
            font-size: clamp(38px, 7vw, 76px);
            line-height: 0.95;
            letter-spacing: 0;
        }

        .hero p,
        .content__lead {
            max-width: 680px;
            margin: 22px 0 0;
            color: rgba(255, 253, 250, 0.82);
            font-size: 18px;
        }

        .section {
            padding: 48px 0;
        }

        .section__heading {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 24px;
            margin-bottom: 20px;
        }

        .section__heading h2 {
            margin: 0;
            font-size: 28px;
            line-height: 1.1;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .card {
            min-height: 180px;
            padding: 24px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--surface);
            text-decoration: none;
            box-shadow: 0 14px 36px rgba(44, 35, 24, 0.08);
        }

        .card__meta {
            color: var(--brand);
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .card h3 {
            margin: 10px 0;
            font-size: 22px;
            line-height: 1.15;
        }

        .card p,
        .muted {
            color: var(--muted);
        }

        .content {
            width: min(860px, calc(100% - 32px));
            margin: 0 auto;
            padding: 56px 0;
        }

        .content h1 {
            color: var(--ink);
        }

        .content__lead {
            color: var(--muted);
        }

        .content__body {
            margin-top: 34px;
            font-size: 18px;
        }

        .site-footer {
            margin-top: auto;
            border-top: 1px solid var(--line);
            padding: 28px 0;
            color: var(--muted);
            background: var(--surface);
            font-size: 14px;
        }

        @media (max-width: 760px) {
            .site-header__inner {
                align-items: flex-start;
                flex-direction: column;
                padding: 16px 0;
            }

            .nav {
                flex-wrap: wrap;
            }

            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="site-shell">
        <header class="site-header">
            <div class="site-header__inner">
                <a class="brand" href="{{ route('public.home') }}">
                    VYD Abogados
                    <span>Estudio jurídico</span>
                </a>

                <nav class="nav" aria-label="Navegación principal">
                    <a href="{{ route('public.home') }}">Inicio</a>
                    <a href="{{ route('public.about') }}">Nosotros</a>
                    <a href="{{ route('public.team') }}">Equipo</a>
                    <a href="{{ route('public.practice-areas.index') }}">Áreas</a>
                    <a href="{{ route('public.contact') }}">Contacto</a>
                </nav>
            </div>
        </header>

        <main>
            @yield('content')
        </main>

        <footer class="site-footer">
            <div class="section" style="padding: 0;">
                © {{ now()->year }} VYD Abogados. Sitio público en Laravel.
            </div>
        </footer>
    </div>
</body>
</html>
