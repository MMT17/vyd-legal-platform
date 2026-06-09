@extends('public.layouts.app', [
    'title' => 'Áreas de práctica | VYD Abogados',
    'description' => 'Áreas de práctica de VYD Abogados.',
])

@section('content')
    <section class="content">
        <h1>Áreas de práctica</h1>
        <p class="content__lead">Listado de áreas activas del CMS público.</p>
    </section>

    <section class="section" style="padding-top: 0;">
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
                    <h3>No hay áreas activas</h3>
                    <p>Publica áreas de práctica desde Filament.</p>
                </article>
            @endforelse
        </div>

        <div style="margin-top: 28px;">
            {{ $practiceAreas->links() }}
        </div>
    </section>
@endsection
