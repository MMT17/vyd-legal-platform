@extends('public.layouts.app', [
    'title' => $practiceArea->title . ' | VYD Abogados',
    'description' => $practiceArea->excerpt,
])

@section('content')
    <article class="content">
        @if ($practiceArea->image_path)
            <img class="content-image" src="{{ asset('storage/' . $practiceArea->image_path) }}" alt="{{ $practiceArea->title }}">
        @else
            <div class="content-image placeholder-image">&Aacute;rea</div>
        @endif

        <p class="eyebrow">Area de pr&aacute;ctica</p>
        <h1 class="page-title">{{ $practiceArea->title }}</h1>

        @if ($practiceArea->excerpt)
            <p class="lead">{{ $practiceArea->excerpt }}</p>
        @endif

        @if ($practiceArea->content)
            <div class="content-body">{!! $practiceArea->content !!}</div>
        @endif

        <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 34px;">
            <a class="button button--secondary" href="{{ route('public.practice-areas.index') }}">Volver a &aacute;reas</a>
            <a class="button" href="{{ route('public.contact') }}">Contactar</a>
        </div>
    </article>
@endsection
