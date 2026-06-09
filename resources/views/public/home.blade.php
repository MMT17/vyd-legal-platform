@extends('public.layouts.app', [
    'title' => ($page?->meta_title ?: 'VYD Abogados'),
    'description' => $page?->meta_description ?: 'Sitio público de VYD Abogados.',
])

@section('content')
    <section class="hero">
        <div class="section" style="padding: 0;">
            <h1>{{ $page?->title ?? 'VYD Abogados' }}</h1>
            <p>Asesoría jurídica con foco en gestión legal, litigios y acompañamiento estratégico para organizaciones.</p>
        </div>
    </section>

    <section class="section">
        <div class="section__heading">
            <h2>Contenido principal</h2>
        </div>

        <div class="grid">
            @forelse ($page?->activeSections ?? collect() as $section)
                <article class="card">
                    <span class="card__meta">{{ $section->key ?: 'Sección' }}</span>
                    <h3>{{ $section->title }}</h3>
                    @if ($section->subtitle)
                        <p>{{ $section->subtitle }}</p>
                    @endif
                    @if ($section->content)
                        <div>{!! $section->content !!}</div>
                    @endif
                </article>
            @empty
                <article class="card">
                    <span class="card__meta">CMS público</span>
                    <h3>Home sin secciones activas</h3>
                    <p>Crea la página con slug <strong>home</strong> y sus secciones desde Filament.</p>
                </article>
            @endforelse
        </div>
    </section>

    <section class="section">
        <div class="section__heading">
            <h2>Áreas de práctica</h2>
            <a class="muted" href="{{ route('public.practice-areas.index') }}">Ver todas</a>
        </div>

        <div class="grid">
            @forelse ($practiceAreas as $area)
                <a class="card" href="{{ route('public.practice-areas.show', $area->slug) }}">
                    <span class="card__meta">{{ $area->icon ?: 'Área' }}</span>
                    <h3>{{ $area->title }}</h3>
                    @if ($area->excerpt)
                        <p>{{ $area->excerpt }}</p>
                    @endif
                </a>
            @empty
                <article class="card">
                    <span class="card__meta">Áreas</span>
                    <h3>Sin áreas activas</h3>
                    <p>Las áreas de práctica activas aparecerán en este bloque.</p>
                </article>
            @endforelse
        </div>
    </section>
@endsection
