@extends('public.layouts.app', [
    'title' => 'Areas de practica | VYD Abogados',
    'description' => 'Areas de practica de VYD Abogados.',
])

@section('content')
    <section class="content">
        <p class="eyebrow">Servicios</p>
        <h1 class="page-title">&Aacute;reas de pr&aacute;ctica</h1>
    </section>

    <section class="section section--tight">
        <div class="container">
            <div class="grid">
                @forelse ($practiceAreas as $area)
                    <article class="card area-card">
                        @if ($area->image_path)
                            <img class="card__image" src="{{ asset('storage/' . $area->image_path) }}" alt="{{ $area->title }}">
                        @else
                            <div class="placeholder-image">&Aacute;rea</div>
                        @endif
                        <h3>{{ $area->title }}</h3>
                    </article>
                @empty
                    <article class="card">
                        <div class="placeholder-image">&Aacute;rea</div>
                        <h3>No hay &aacute;reas activas</h3>
                    </article>
                @endforelse
            </div>

            <div style="margin-top: 30px;">
                {{ $practiceAreas->links() }}
            </div>
        </div>
    </section>
@endsection
