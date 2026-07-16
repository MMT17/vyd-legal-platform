@php
    $initials = collect(explode(' ', $member->name))
        ->filter()
        ->map(fn (string $part): string => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
        ->take(2)
        ->implode('');

    $displayPosition = $member->position;
@endphp

<article class="profile-card">
    @if ($member->photo_path)
        <img class="profile-card__photo" src="{{ asset('storage/' . $member->photo_path) }}" alt="{{ $member->name }}">
    @else
        <div class="profile-card__photo profile-card__photo--placeholder">{{ $initials ?: $placeholder }}</div>
    @endif

    <div class="profile-card__body">
        <h3>{{ $member->name }}</h3>

        @if ($displayPosition)
            <p class="profile-card__position">{{ $displayPosition }}</p>
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

        @if ($member->email || $member->linkedin_url)
            <div class="profile-card__links">
                @if ($member->email)
                    <a href="mailto:{{ $member->email }}"><span aria-hidden="true">&#9993;</span>{{ $member->email }}</a>
                @endif

                @if ($member->linkedin_url)
                    <a href="{{ $member->linkedin_url }}" target="_blank" rel="noopener">LinkedIn</a>
                @endif
            </div>
        @endif
    </div>
</article>
