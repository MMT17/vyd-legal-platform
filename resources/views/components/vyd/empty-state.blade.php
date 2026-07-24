@props([
    'title',
    'description',
    'icon' => 'heroicon-o-information-circle',
    'actionLabel' => null,
    'actionUrl' => null,
    'tone' => 'neutral',
    'compact' => false,
])

@php
    $toneClasses = [
        'neutral' => 'text-amber-600 dark:text-amber-300',
        'success' => 'text-green-600 dark:text-green-300',
        'info' => 'text-blue-600 dark:text-blue-300',
    ];
@endphp

<div {{ $attributes->class(['vyd-card text-center', $compact ? 'p-5' : 'p-8']) }}>
    <x-filament::icon :icon="$icon" class="{{ $compact ? 'h-8 w-8' : 'h-10 w-10' }} mx-auto {{ $toneClasses[$tone] ?? $toneClasses['neutral'] }}" />
    <h3 class="mt-3 text-base font-semibold text-[color:var(--vyd-text)]">{{ $title }}</h3>
    <p class="mx-auto mt-1 max-w-md text-sm leading-6 vyd-muted">{{ $description }}</p>
    @if ($actionLabel && $actionUrl)
        <a href="{{ $actionUrl }}" class="vyd-focusable mt-4 inline-flex rounded-lg bg-[color:var(--vyd-gold-600)] px-4 py-2 text-sm font-semibold text-white">
            {{ $actionLabel }}
        </a>
    @endif
</div>
