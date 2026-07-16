<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'VYD Abogados | Estudio Jur&iacute;dico' }}</title>
    <meta name="description" content="{{ $description ?? 'VYD Abogados | Estudio Jur&iacute;dico' }}">
    <link rel="icon" type="image/png" href="{{ asset('images/branding/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            --primary: #0F2744;
            --secondary: #C89B3C;
            --accent: #C89B3C;
            --white: #FFFFFF;
            --bg: #F6F2EB;
            --ink: #1C1C1C;
            --muted: #68635b;
            --line: #ded6c8;
            --paper: #FFFFFF;
            --soft: #EEE7DC;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: "Plus Jakarta Sans", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-weight: 400;
            line-height: 1.6;
        }

        a { color: inherit; }
        img { max-width: 100%; }

        .site-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            width: min(1120px, calc(100% - 32px));
            margin: 0 auto;
        }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 20;
            border-bottom: 1px solid rgba(222, 214, 200, 0.9);
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(14px);
        }

        .site-header__inner {
            min-height: 76px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 28px;
        }

        .nav {
            display: flex;
            align-items: center;
            gap: 20px;
            color: var(--muted);
            font-size: 14px;
            font-weight: 500;
        }

        .nav a,
        .footer-nav a {
            text-decoration: none;
        }

        .nav a:hover,
        .footer-nav a:hover {
            color: var(--primary);
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: fit-content;
            min-height: 46px;
            padding: 11px 20px;
            border: 1px solid var(--accent);
            border-radius: 6px;
            background: var(--accent);
            color: var(--white);
            font-weight: 700;
            text-decoration: none;
        }

        .button--secondary {
            background: transparent;
            color: var(--primary);
        }

        .button--light {
            border-color: rgba(255, 255, 255, 0.76);
            background: rgba(255, 255, 255, 0.08);
            color: var(--white);
        }

        .hero {
            padding: 92px 0 72px;
            border-bottom: 1px solid var(--line);
            background: linear-gradient(120deg, rgba(15, 39, 68, 0.94), rgba(15, 39, 68, 0.78)), var(--primary);
            color: var(--white);
        }

        .home-hero {
            padding: 30px 0 0;
            background: var(--bg);
        }

        .home-hero .container { position: relative; }

        .home-hero__image,
        .home-hero__placeholder {
            width: 100%;
            min-height: 360px;
            max-height: 560px;
            border-radius: 8px;
            object-fit: cover;
            box-shadow: 0 18px 46px rgba(15, 39, 68, 0.16);
        }

        .home-hero__placeholder {
            display: grid;
            place-items: center;
            background:
                radial-gradient(circle at 72% 22%, rgba(182, 140, 74, 0.38), transparent 28%),
                linear-gradient(120deg, rgba(15, 39, 68, 0.96), rgba(29, 59, 99, 0.82)),
                var(--primary);
            color: rgba(255, 255, 255, 0.2);
            font-size: clamp(72px, 18vw, 210px);
            font-weight: 700;
            letter-spacing: 0;
        }

        .home-hero__text {
            position: absolute;
            left: clamp(24px, 6vw, 70px);
            bottom: clamp(24px, 6vw, 70px);
            color: var(--white);
        }

        .home-hero__text h1,
        .hero h1,
        .page-title {
            max-width: 880px;
            margin: 0;
            font-size: clamp(38px, 7vw, 76px);
            font-weight: 700;
            line-height: 0.98;
            letter-spacing: 0;
        }

        .home-hero__text h1 {
            font-size: clamp(42px, 8vw, 86px);
            line-height: 0.95;
        }

        .home-hero__text p,
        .hero p,
        .lead {
            max-width: 720px;
            margin: 22px 0 0;
            color: rgba(255, 255, 255, 0.86);
            font-size: 19px;
            font-weight: 400;
        }

        .home-hero__text p {
            margin-top: 12px;
            font-size: 20px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .home-hero__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 26px;
        }

        .section { padding: 56px 0; }
        .section--tight { padding-top: 28px; }

        .section-actions {
            margin-top: 26px;
        }

        .section-heading {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 24px;
        }

        .section-heading h2,
        .card h3,
        .profile-card__body h3 {
            margin: 0;
            color: var(--ink);
            font-weight: 700;
            line-height: 1.12;
        }

        .section-heading h2 { font-size: 30px; }
        .card h3 { margin: 16px 0 8px; font-size: 22px; }
        .profile-card__body h3 { margin: 8px 0 10px; font-size: 25px; }

        .eyebrow,
        .card__meta {
            margin: 0 0 10px;
            color: var(--accent);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .grid,
        .profile-grid {
            display: grid;
            gap: 20px;
        }

        .grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .profile-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 22px; }

        .card,
        .profile-card {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--paper);
            box-shadow: 0 16px 38px rgba(15, 39, 68, 0.08);
        }

        .card {
            display: flex;
            flex-direction: column;
            min-height: 100%;
            padding: 22px;
            text-decoration: none;
        }

        .profile-card {
            display: grid;
            grid-template-columns: 190px minmax(0, 1fr);
            gap: 22px;
            min-height: 280px;
            padding: 22px;
        }

        .card p,
        .muted,
        .profile-card__description {
            color: var(--muted);
        }

        .card__image,
        .placeholder-image,
        .content-image,
        .profile-card__photo {
            width: 100%;
            border-radius: 7px;
            background: linear-gradient(135deg, rgba(182, 140, 74, 0.2), rgba(15, 39, 68, 0.1)), var(--soft);
        }

        .card__image,
        .placeholder-image {
            aspect-ratio: 16 / 10;
            object-fit: cover;
        }

        .placeholder-image,
        .profile-card__photo--placeholder {
            display: grid;
            place-items: center;
            color: var(--primary);
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .card__link {
            margin-top: auto;
            color: var(--primary);
            font-size: 14px;
            font-weight: 700;
        }

        .profile-card__photo {
            height: 100%;
            min-height: 236px;
            object-fit: cover;
        }

        .profile-card__description { margin: 0 0 16px; }

        .area-card h3 {
            margin-bottom: 0;
        }

        .profile-card__name {
            color: inherit;
            text-decoration: none;
        }

        .profile-card__name:hover { color: var(--primary); }

        .profile-card__position {
            margin: 0 0 14px;
            color: var(--primary);
            font-size: 14px;
            font-weight: 700;
        }

        .profile-card__links {
            display: grid;
            gap: 8px;
            margin-top: 18px;
            color: var(--primary);
            font-size: 14px;
            font-weight: 700;
        }

        .profile-card__links a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            text-decoration: none;
        }

        .profile-section { margin-top: 18px; }

        .profile-section h3,
        .profile-section h4 {
            margin: 0 0 8px;
            color: var(--primary);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .profile-section ul {
            display: grid;
            gap: 6px;
            margin: 0;
            padding-left: 18px;
            color: var(--muted);
            font-size: 14px;
        }

        .specialty-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .specialty-list li {
            padding: 6px 10px;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: #f8f2e8;
            color: var(--primary);
            font-size: 13px;
            font-weight: 500;
        }

        .content {
            width: min(900px, calc(100% - 32px));
            margin: 0 auto;
            padding: 62px 0;
        }

        .content .lead { color: var(--muted); }
        .content-body { margin-top: 32px; font-size: 18px; }

        .home-intro {
            max-width: 780px;
            margin-top: 0;
        }

        .content--centered {
            text-align: center;
        }

        .content--centered .content-body {
            max-width: 760px;
            margin-right: auto;
            margin-left: auto;
        }

        .cms-section {
            margin-bottom: 32px;
        }

        .cms-section:last-child {
            margin-bottom: 0;
        }

        .content-image {
            display: block;
            min-height: 260px;
            max-height: 440px;
            margin-bottom: 30px;
            object-fit: cover;
        }

        .simple-form {
            display: grid;
            gap: 16px;
            margin-top: 30px;
        }

        .simple-form input,
        .simple-form textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 7px;
            background: var(--paper);
            padding: 13px 15px;
            color: var(--ink);
            font: inherit;
        }

        .notice {
            padding: 13px 16px;
            border: 1px solid #bbd7bc;
            border-radius: 7px;
            background: #eef8ef;
            color: #285d31;
        }

        .profile-detail {
            display: grid;
            grid-template-columns: minmax(220px, 340px) minmax(0, 1fr);
            gap: 42px;
            align-items: start;
        }

        .profile-detail__photo {
            width: 100%;
            aspect-ratio: 4 / 5;
            border-radius: 8px;
            object-fit: cover;
            background: linear-gradient(135deg, rgba(182, 140, 74, 0.2), rgba(15, 39, 68, 0.1)), var(--soft);
        }

        .profile-detail__photo--placeholder {
            display: grid;
            place-items: center;
            color: var(--primary);
            font-size: 54px;
            font-weight: 700;
            letter-spacing: 0;
        }

        .profile-detail__links {
            display: grid;
            gap: 10px;
            margin-top: 18px;
            color: var(--primary);
            font-weight: 700;
        }

        .profile-detail__links a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .profile-detail__body {
            border-top: 1px solid var(--line);
            padding-top: 28px;
        }

        .profile-detail__body h2 {
            margin: 0;
            color: var(--ink);
            font-size: clamp(30px, 4vw, 48px);
            line-height: 1.05;
        }

        .profile-detail__position {
            margin: 10px 0 0;
            color: var(--primary);
            font-size: 18px;
            font-weight: 700;
        }

        .site-footer {
            margin-top: auto;
            border-top: 1px solid var(--line);
            padding: 42px 0 28px;
            background: var(--paper);
            color: var(--muted);
            font-size: 14px;
        }

        .site-footer__inner {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr);
            gap: 18px;
        }

        .footer-brand {
            display: grid;
            gap: 10px;
            max-width: 460px;
        }

        .footer-brand strong {
            color: var(--ink);
            font-size: 18px;
        }

        .footer-brand p { margin: 4px 0 0; }

        .footer-brand > span {
            color: var(--accent);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .footer-contact {
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
        }

        .footer-meta {
            display: grid;
            justify-items: end;
            gap: 18px;
            text-align: right;
        }

        .footer-nav {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 12px 18px;
        }

        .copyright {
            margin: 0;
            color: var(--muted);
        }

        @media (max-width: 820px) {
            .site-header__inner,
            .site-footer__inner,
            .profile-card,
            .profile-detail {
                grid-template-columns: 1fr;
            }

            .site-header__inner {
                align-items: flex-start;
                padding: 18px 0;
            }

            .nav,
            .footer-nav {
                flex-wrap: wrap;
                justify-content: flex-start;
                gap: 12px 16px;
            }

            .footer-meta {
                justify-items: start;
                text-align: left;
            }

            .grid,
            .profile-grid {
                grid-template-columns: 1fr;
            }

            .home-hero__image,
            .home-hero__placeholder {
                min-height: 320px;
            }

            .home-hero__actions .button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="site-shell">
        <header class="site-header">
            <div class="container site-header__inner">
                <x-branding.logo :href="route('public.home')" variant="horizontal" />

                <nav class="nav" aria-label="Navegaci&oacute;n principal">
                    <a href="{{ route('public.home') }}">Inicio</a>
                    <a href="{{ route('public.about') }}">Nosotros</a>
                    <a href="{{ route('public.team') }}">Equipo</a>
                    <a href="{{ route('public.practice-areas.index') }}">&Aacute;reas</a>
                    <a href="{{ route('public.contact') }}">Contacto</a>
                </nav>
            </div>
        </header>

        <main>
            @yield('content')
        </main>

        <footer class="site-footer">
            <div class="container site-footer__inner">
                <div class="footer-brand">
                    <x-branding.logo :href="route('public.home')" variant="horizontal" />
                    <strong>VYD Abogados</strong>
                    <span>Estudio Jur&iacute;dico</span>
                    <a class="footer-contact" href="mailto:contacto@vydabogados.cl">contacto@vydabogados.cl</a>
                </div>
                <div class="footer-meta">
                    <nav class="footer-nav" aria-label="Navegaci&oacute;n secundaria">
                        <a href="{{ route('public.home') }}">Inicio</a>
                        <a href="{{ route('public.about') }}">Nosotros</a>
                        <a href="{{ route('public.team') }}">Equipo</a>
                        <a href="{{ route('public.practice-areas.index') }}">&Aacute;reas</a>
                        <a href="{{ route('public.contact') }}">Contacto</a>
                    </nav>
                    <p class="copyright">&copy; {{ now()->year }} VYD Abogados</p>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
