@props([
    'variant' => 'horizontal',
    'href' => null,
])

@php
    $file = match ($variant) {
        'vertical' => 'logo-vertical.png',
        'icon' => 'logo-icon.png',
        default => 'logo-horizontal.png',
    };

    $relativePath = 'images/branding/' . $file;
    $hasImage = file_exists(public_path($relativePath));

    $classes = [
        'brand-logo',
        'brand-logo--' . $variant,
    ];
@endphp

@once
    <style>
        .brand-logo {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            color: #0F2744;
            font-family: "Plus Jakarta Sans", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            text-decoration: none;
        }

        .brand-logo img {
            display: block;
            width: auto;
            max-width: 100%;
            height: auto;
        }

        .brand-logo--horizontal img { max-height: 48px; }
        .brand-logo--vertical img { max-height: 94px; }
        .brand-logo--icon img { max-height: 42px; }

        .brand-logo__fallback {
            display: grid;
            gap: 2px;
            line-height: 1;
        }

        .brand-logo__monogram {
            color: #0F2744;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 0;
        }

        .brand-logo__name {
            color: #0F2744;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
        }

        .brand-logo__label {
            color: #C89B3C;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.18em;
        }
    </style>
@endonce

@php
    $content = $hasImage
        ? '<img src="' . e(asset($relativePath)) . '" alt="Vicencio &amp; Dom&iacute;nguez Abogados">'
        : '<span class="brand-logo__fallback" aria-hidden="true"><span class="brand-logo__monogram">VD</span><span class="brand-logo__name">VICENCIO &amp; DOM&Iacute;NGUEZ</span><span class="brand-logo__label">ABOGADOS</span></span>';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes)->merge(['aria-label' => 'Vicencio & Domínguez Abogados']) }}>
        {!! $content !!}
    </a>
@else
    <span {{ $attributes->class($classes)->merge(['role' => 'img', 'aria-label' => 'Vicencio & Domínguez Abogados']) }}>
        {!! $content !!}
    </span>
@endif
