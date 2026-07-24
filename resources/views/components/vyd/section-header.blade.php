@props(['title', 'description' => null, 'icon' => null])

<div {{ $attributes->class('flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between') }}>
    <div class="flex gap-3">
        @if ($icon)
            <x-filament::icon :icon="$icon" class="mt-1 h-5 w-5 shrink-0 text-amber-600 dark:text-amber-300" />
        @endif
        <div>
            <h2 class="vyd-section-title">{{ $title }}</h2>
            @if ($description)
                <p class="mt-1 text-sm vyd-muted">{{ $description }}</p>
            @endif
        </div>
    </div>
    @if (! $slot->isEmpty())
        <div class="flex shrink-0 flex-wrap gap-2">{{ $slot }}</div>
    @endif
</div>
