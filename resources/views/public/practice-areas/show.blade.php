@extends('public.layouts.app', [
    'title' => $practiceArea->title . ' | VYD Abogados',
    'description' => $practiceArea->excerpt,
])

@section('content')
    <article class="content">
        <p class="card__meta">{{ $practiceArea->icon ?: 'Área de práctica' }}</p>
        <h1>{{ $practiceArea->title }}</h1>

        @if ($practiceArea->excerpt)
            <p class="content__lead">{{ $practiceArea->excerpt }}</p>
        @endif

        @if ($practiceArea->content)
            <div class="content__body">{!! $practiceArea->content !!}</div>
        @endif
    </article>
@endsection
