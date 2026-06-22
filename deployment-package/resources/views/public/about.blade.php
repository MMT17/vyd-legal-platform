@extends('public.layouts.app', [
    'title' => ($page?->meta_title ?: 'Nosotros | VYD Abogados'),
    'description' => $page?->meta_description ?: 'Informacion institucional de VYD Abogados.',
])

@section('content')
    <article class="content">
        <p class="eyebrow">Nosotros</p>
        <h1 class="page-title">Nosotros</h1>
        <p class="lead">
            VYD Abogados es un estudio jur&iacute;dico orientado a entregar asesor&iacute;a clara,
            seria y estrat&eacute;gica a personas y empresas.
        </p>

        <div class="content-body">
            @php
                $sections = $page?->activeSections ?? collect();
            @endphp

            @if ($sections->isNotEmpty())
                @foreach ($sections as $section)
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
                @endforeach
            @else
                <p class="muted">
                    Nuestro trabajo se enfoca en comprender cada caso, ordenar sus aspectos relevantes
                    y acompa&ntilde;ar decisiones legales con criterio profesional y sentido pr&aacute;ctico.
                </p>
            @endif
        </div>
    </article>

    <section class="section section--tight">
        <div class="container">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Valores</p>
                    <h2>La forma en que trabajamos</h2>
                </div>
            </div>

            <div class="grid">
                <article class="card">
                    <span class="card__meta">01</span>
                    <h3>Compromiso</h3>
                    <p>Abordamos cada asunto con responsabilidad, dedicaci&oacute;n y seguimiento oportuno.</p>
                </article>
                <article class="card">
                    <span class="card__meta">02</span>
                    <h3>Confianza</h3>
                    <p>Construimos relaciones profesionales basadas en claridad, reserva y comunicaci&oacute;n honesta.</p>
                </article>
                <article class="card">
                    <span class="card__meta">03</span>
                    <h3>Estrategia</h3>
                    <p>Buscamos soluciones legales con una mirada pr&aacute;ctica, seria y orientada a resultados.</p>
                </article>
            </div>
        </div>
    </section>
@endsection
