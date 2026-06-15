@extends('public.layouts.app', [
    'title' => 'Areas de practica | VYD Abogados',
    'description' => 'Areas de practica de VYD Abogados.',
])

@section('content')
    <section class="content">
        <p class="eyebrow">Servicios</p>
        <h1 class="page-title">&Aacute;reas de pr&aacute;ctica</h1>
        <p class="lead">Asesor&iacute;a legal simple, seria y orientada a resolver necesidades concretas.</p>
    </section>

    <section class="section section--tight">
        <div class="container">
            <div class="grid">
                @forelse ($practiceAreas as $area)
                    <a class="card" href="{{ route('public.practice-areas.show', $area->slug) }}">
                        @if ($area->image_path)
                            <img class="card__image" src="{{ asset('storage/' . $area->image_path) }}" alt="{{ $area->title }}">
                        @else
                            <div class="placeholder-image">&Aacute;rea</div>
                        @endif
                        <h3>{{ $area->title }}</h3>
                        @if ($area->excerpt)
                            <p>{{ $area->excerpt }}</p>
                        @endif
                        <span class="card__link">Ver m&aacute;s</span>
                    </a>
                @empty
                    <article class="card">
                        <div class="placeholder-image">&Aacute;rea</div>
                        <h3>No hay &aacute;reas activas</h3>
                        <p>Publica &aacute;reas de pr&aacute;ctica desde Filament.</p>
                    </article>
                @endforelse
            </div>

            <div style="margin-top: 30px;">
                {{ $practiceAreas->links() }}
            </div>
        </div>
    </section>
@endsection
