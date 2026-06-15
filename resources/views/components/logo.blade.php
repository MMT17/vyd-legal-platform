@props([
    'href' => null,
    'size' => 'md',
    'inverted' => false,
])

@php
    $classes = [
        'vyd-logo',
        'vyd-logo--' . $size,
        'vyd-logo--inverted' => $inverted,
    ];
@endphp

@once
    <style>
        .vyd-logo {
            --vyd-logo-primary: var(--primary, #0F2744);
            --vyd-logo-accent: var(--accent, #B68C4A);
            display: inline-flex;
            flex-direction: column;
            width: fit-content;
            color: var(--vyd-logo-primary);
            font-family: "Plus Jakarta Sans", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            line-height: 0.92;
            letter-spacing: 0.02em;
            text-decoration: none;
        }

        .vyd-logo--inverted {
            --vyd-logo-primary: #fffdf8;
            --vyd-logo-accent: #D6B16E;
        }

        .vyd-logo__mark {
            font-weight: 700;
            letter-spacing: 0;
        }

        .vyd-logo__label {
            margin-top: 0.42em;
            color: var(--vyd-logo-accent);
            font-weight: 700;
            letter-spacing: 0.22em;
            text-transform: uppercase;
        }

        .vyd-logo--sm .vyd-logo__mark { font-size: clamp(24px, 6vw, 28px); }
        .vyd-logo--sm .vyd-logo__label { font-size: 9px; }

        .vyd-logo--md .vyd-logo__mark { font-size: clamp(30px, 7vw, 36px); }
        .vyd-logo--md .vyd-logo__label { font-size: 11px; }

        .vyd-logo--lg .vyd-logo__mark { font-size: clamp(42px, 10vw, 64px); }
        .vyd-logo--lg .vyd-logo__label { font-size: clamp(12px, 2.4vw, 15px); }
    </style>
@endonce

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes)->merge(['aria-label' => 'VYD Abogados']) }}>
        <span class="vyd-logo__mark">VYD</span>
        <span class="vyd-logo__label">Abogados</span>
    </a>
@else
    <div {{ $attributes->class($classes)->merge(['role' => 'img', 'aria-label' => 'VYD Abogados']) }}>
        <span class="vyd-logo__mark">VYD</span>
        <span class="vyd-logo__label">Abogados</span>
    </div>
@endif
