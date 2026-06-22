@extends('public.layouts.app', [
    'title' => ($page?->meta_title ?: 'Nosotros | VYD Abogados'),
    'description' => $page?->meta_description ?: 'Informacion institucional de VYD Abogados.',
])

@section('content')
    <article class="content content--centered">
        <p class="eyebrow">Nosotros</p>
        <h1 class="page-title">Nosotros</h1>

        <div class="content-body">
            @php
                $sections = ($page?->activeSections ?? collect())
                    ->reject(function ($section): bool {
                        $text = \Illuminate\Support\Str::lower(\Illuminate\Support\Str::ascii(implode(' ', [
                            $section->key,
                            $section->title,
                            $section->subtitle,
                        ])));

                        return \Illuminate\Support\Str::contains($text, [
                            'educacion',
                            'education',
                            'equipo',
                            'team',
                            'valores',
                            'values',
                        ]);
                    });
            @endphp

            @if ($sections->isNotEmpty())
                @foreach ($sections as $section)
                    <section class="cms-section">
                        @if ($section->image_path)
                            <img class="content-image" src="{{ asset('storage/' . $section->image_path) }}" alt="{{ $section->title ?: 'VYD Abogados' }}">
                        @endif
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
                @endforeach
            @else
                <p class="muted">
                    VYD Abogados entrega asesor&iacute;a jur&iacute;dica especializada a personas y empresas,
                    con una mirada cercana, seria y orientada a resultados.
                </p>
            @endif
        </div>
    </article>
@endsection
