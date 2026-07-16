@extends('public.layouts.app', [
    'title' => ($page?->meta_title ?: 'VYD Abogados'),
    'description' => $page?->meta_description ?: 'Sitio institucional de VYD Abogados.',
])

@section('content')
    @php
        $intro = ($page?->activeSections ?? collect())
            ->first(fn ($section) => filled($section->content) || filled($section->title));
    @endphp

    <section class="home-hero">
        <div class="container">
            @if ($heroSection?->image_path)
                <img class="home-hero__image" src="{{ asset('storage/' . $heroSection->image_path) }}" alt="VYD Abogados">
            @else
                <div class="home-hero__placeholder">VYD</div>
            @endif

            <div class="home-hero__text">
                <h1>VYD Abogados</h1>
                <div class="home-hero__actions">
                    <a class="button" href="{{ route('public.contact') }}">Contactar ahora</a>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Nosotros</p>
                    <h2>{{ $intro?->title ?: 'VYD Abogados' }}</h2>
                </div>
            </div>

            <div class="content-body home-intro">
                @if ($intro?->content)
                    {!! $intro->content !!}
                @else
                    <p class="muted">
                        VYD Abogados entrega asesor&iacute;a jur&iacute;dica especializada a personas y empresas,
                        con una mirada cercana, seria y orientada a resultados.
                    </p>
                @endif
            </div>

            <div class="section-actions">
                <a class="button button--secondary" href="{{ route('public.contact') }}">Contactar</a>
            </div>
        </div>
    </section>
@endsection
