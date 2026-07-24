@php
    $profileId = 'team-profile-' . $member->id;
    $initials = collect(explode(' ', $member->name))
        ->filter()
        ->map(fn (string $part): string => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
        ->take(2)
        ->implode('');

    $displayPosition = $member->position;
@endphp

<article class="profile-card" data-team-card data-member-name="{{ $member->name }}">
    <button
        class="profile-card__summary"
        type="button"
        aria-expanded="false"
        aria-controls="{{ $profileId }}"
        data-team-toggle
    >
        @if ($member->photo_path)
            <img class="profile-card__photo" src="{{ asset('storage/' . $member->photo_path) }}" alt="{{ $member->name }}">
        @else
            <span class="profile-card__photo profile-card__photo--placeholder">{{ $initials ?: $placeholder }}</span>
        @endif

        <span class="profile-card__name">{{ $member->name }}</span>
    </button>

    <template data-team-template>
        <div class="profile-detail-panel" id="{{ $profileId }}" data-team-detail>
            <div>
                @if ($member->photo_path)
                    <img class="profile-detail-panel__photo" src="{{ asset('storage/' . $member->photo_path) }}" alt="{{ $member->name }}">
                @else
                    <div class="profile-detail-panel__photo profile-detail-panel__photo--placeholder">{{ $initials ?: $placeholder }}</div>
                @endif
            </div>

            <div class="profile-detail-panel__body">
                <button class="profile-detail-panel__close" type="button" data-team-close aria-label="Cerrar perfil">
                    Cerrar
                </button>

                <h3>{{ $member->name }}</h3>

                @if ($displayPosition)
                    <p class="profile-card__position">{{ $displayPosition }}</p>
                @endif

                @if ($member->bio || $member->short_description)
                    <div class="profile-section">
                        <h4>Informaci&oacute;n profesional</h4>
                        <p>{{ $member->bio ?: $member->short_description }}</p>
                    </div>
                @endif

                @if (filled($member->specialties))
                    <div class="profile-section">
                        <h4>&Aacute;reas de pr&aacute;ctica</h4>
                        <ul>
                            @foreach ($member->specialties as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (filled($member->education))
                    <div class="profile-section">
                        <h4>Educaci&oacute;n</h4>
                        <ul>
                            @foreach ($member->education as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (filled($member->experience))
                    <div class="profile-section">
                        <h4>Experiencia</h4>
                        <ul>
                            @foreach ($member->experience as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (filled($member->activities))
                    <div class="profile-section">
                        <h4>Trabajos anteriores</h4>
                        <ul>
                            @foreach ($member->activities as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($member->email)
                    <div class="profile-section profile-section--contact">
                        <h4>Correo</h4>
                        <a href="mailto:{{ $member->email }}"><span aria-hidden="true">&#9993;</span>{{ $member->email }}</a>
                    </div>
                @endif

                @if ($member->linkedin_url)
                    <div class="profile-card__links">
                        @if ($member->linkedin_url)
                            <a href="{{ $member->linkedin_url }}" target="_blank" rel="noopener">LinkedIn</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </template>
</article>
