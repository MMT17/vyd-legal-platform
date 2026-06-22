@extends('public.layouts.app', [
    'title' => ($page?->meta_title ?: 'VYD Abogados'),
    'description' => $page?->meta_description ?: 'Sitio institucional de VYD Abogados.',
])

@section('content')
    <section class="home-hero">
        <div class="container">
            @if ($heroSection?->image_path)
                <img class="home-hero__image" src="{{ asset('storage/' . $heroSection->image_path) }}" alt="VYD Abogados">
            @else
                <div class="home-hero__placeholder">VYD</div>
            @endif

            <div class="home-hero__text">
                <h1>VYD Abogados</h1>
                <p>Estudio jur&iacute;dico</p>
                <div class="home-hero__actions">
                    <a class="button" href="{{ route('public.contact') }}">Contactar ahora</a>
                    <a class="button button--light" href="{{ route('public.about') }}">Conocer el estudio</a>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Institucional</p>
                    <h2>Nosotros</h2>
                </div>
            </div>

            @php
                $intro = ($page?->activeSections ?? collect())->first();
            @endphp

            <div class="content-body" style="max-width: 780px; margin-top: 0;">
                @if ($intro?->content)
                    {!! $intro->content !!}
                @else
                    <p class="muted">
                        VYD Abogados es un estudio jur&iacute;dico orientado a entregar asesor&iacute;a clara,
                        seria y estrat&eacute;gica a personas y empresas.
                    </p>
                @endif
            </div>

            <div style="margin-top: 26px;">
                <a class="button button--secondary" href="{{ route('public.about') }}">Conocer m&aacute;s</a>
            </div>
        </div>
    </section>
@endsection
