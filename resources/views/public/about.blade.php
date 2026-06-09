@extends('public.layouts.app', [
    'title' => ($page?->meta_title ?: 'Nosotros | VYD Abogados'),
    'description' => $page?->meta_description ?: 'Información institucional de VYD Abogados.',
])

@section('content')
    <article class="content">
        <h1>{{ $page?->title ?? 'Nosotros' }}</h1>
        <p class="content__lead">Contenido institucional administrado desde el CMS público.</p>

        <div class="content__body">
            @forelse ($page?->activeSections ?? collect() as $section)
                <section style="margin-bottom: 32px;">
                    @if ($section->title)
                        <h2>{{ $section->title }}</h2>
                    @endif
                    @if ($section->subtitle)
                        <p class="muted">{{ $section->subtitle }}</p>
                    @endif
                    @if ($section->content)
                        <div>{!! $section->content !!}</div>
                    @endif
                </section>
            @empty
                <p>Crea la página con slug <strong>nosotros</strong> y agrega secciones activas desde Filament.</p>
            @endforelse
        </div>
    </article>
@endsection
