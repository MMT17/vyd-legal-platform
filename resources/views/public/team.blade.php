@extends('public.layouts.app', [
    'title' => 'Equipo | VYD Abogados',
    'description' => 'Equipo profesional de VYD Abogados.',
])

@php
    $profileCard = function ($member, string $placeholder) {
        return view('public.partials.team-card', [
            'member' => $member,
            'placeholder' => $placeholder,
        ]);
    };
@endphp

@section('content')
    <section class="content">
        <p class="eyebrow">Equipo</p>
        <h1 class="page-title">Nuestro equipo</h1>
        <p class="lead">Conoce a los profesionales que integran VYD Abogados.</p>
    </section>

    <section class="section section--tight">
        <div class="container team-directory">
            <div class="team-directory__section">
                <div class="section-heading">
                    <div>
                        <h2>Socios</h2>
                    </div>
                </div>

                <div class="profile-grid profile-grid--partners">
                    @forelse ($partners as $member)
                        {{ $profileCard($member, 'VYD') }}
                    @empty
                        <article class="card">
                            <div class="placeholder-image">Socios</div>
                            <h3>Socios</h3>
                            <p>Pr&oacute;ximamente incorporaremos informaci&oacute;n de nuestros socios.</p>
                        </article>
                    @endforelse
                </div>
            </div>

            <div class="team-directory__section">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Profesionales</p>
                        <h2>Equipo</h2>
                    </div>
                </div>

                <div class="profile-grid profile-grid--team">
                    @forelse ($teamMembers as $member)
                        {{ $profileCard($member, 'VYD') }}
                    @empty
                        <article class="card">
                            <div class="placeholder-image">Equipo</div>
                            <h3>Equipo profesional</h3>
                            <p>Pr&oacute;ximamente incorporaremos informaci&oacute;n de nuestro equipo profesional.</p>
                        </article>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
