@php
    $initials = collect(explode(' ', $member->name))
        ->filter()
        ->map(fn (string $part): string => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
        ->take(2)
        ->implode('');

    $sections = [
        'Áreas de práctica' => $member->specialties,
        'Educación' => $member->education,
        'Experiencia profesional' => $member->experience,
        'Actividades académicas y profesionales' => $member->activities,
    ];
@endphp

<article class="profile-card">
    @if ($member->photo_path)
        <img class="profile-card__photo" src="{{ asset('storage/' . $member->photo_path) }}" alt="{{ $member->name }}">
    @else
        <div class="profile-card__photo profile-card__photo--placeholder">{{ $initials }}</div>
    @endif

    <div class="profile-card__body">
        @if ($member->position)
            <p class="card__meta">{{ $member->position }}</p>
        @endif

        <h3>{{ $member->name }}</h3>

        @if ($member->short_description)
            <p class="profile-card__description">{{ $member->short_description }}</p>
        @endif

        @foreach ($sections as $title => $items)
            @if (filled($items))
                <div class="profile-section">
                    <h4>{{ $title }}</h4>
                    <ul>
                        @foreach ($items as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endforeach
    </div>
</article>
