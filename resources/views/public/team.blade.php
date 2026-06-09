@extends('public.layouts.app', [
    'title' => 'Equipo | VYD Abogados',
    'description' => 'Equipo profesional de VYD Abogados.',
])

@section('content')
    <section class="content">
        <h1>Equipo</h1>
        <p class="content__lead">Profesionales activos administrados desde el CMS.</p>
    </section>

    <section class="section" style="padding-top: 0;">
        <div class="grid">
            @forelse ($teamMembers as $member)
                <article class="card">
                    <span class="card__meta">{{ $member->position ?: 'Equipo' }}</span>
                    <h3>{{ $member->name }}</h3>
                    @if ($member->email)
                        <p>{{ $member->email }}</p>
                    @endif
                    @if ($member->phone)
                        <p>{{ $member->phone }}</p>
                    @endif
                </article>
            @empty
                <article class="card">
                    <span class="card__meta">Equipo</span>
                    <h3>Sin integrantes activos</h3>
                    <p>Agrega integrantes desde Filament para verlos en esta página.</p>
                </article>
            @endforelse
        </div>
    </section>
@endsection
