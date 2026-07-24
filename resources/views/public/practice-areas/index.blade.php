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
                    @php
                        $areaDescription = \Illuminate\Support\Str::limit(
                            trim(strip_tags($area->excerpt ?: $area->content ?: '')),
                            150
                        );
                    @endphp

                    <article class="area-card" tabindex="0" data-area-card>
                        @if ($area->image_path)
                            <img class="area-card__image" src="{{ asset('storage/' . $area->image_path) }}" alt="{{ $area->title }}">
                        @else
                            <div class="area-card__image area-card__placeholder" aria-hidden="true"></div>
                        @endif

                        <div class="area-card__overlay">
                            <h3>{{ $area->title }}</h3>

                            @if ($areaDescription)
                                <p class="area-card__description">{{ $areaDescription }}</p>
                            @endif
                        </div>
                    </article>
                @empty
                    <article class="card">
                        <div class="placeholder-image" aria-hidden="true"></div>
                        <h3>No hay &aacute;reas activas</h3>
                    </article>
                @endforelse
            </div>

        </div>
    </section>
@endsection
