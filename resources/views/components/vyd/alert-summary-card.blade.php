@props([
    'title',
    'count',
    'description',
    'icon' => 'heroicon-o-bell-alert',
    'tone' => 'neutral',
])

@php
    $toneClasses = [
        'neutral' => [
            'accent' => 'bg-gray-500',
            'icon' => 'bg-gray-100 text-gray-700 ring-gray-200 dark:bg-white/10 dark:text-gray-200 dark:ring-white/10',
            'count' => 'text-gray-950 dark:text-white',
        ],
        'action' => [
            'accent' => 'bg-amber-500',
            'icon' => 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-950/30 dark:text-amber-300 dark:ring-amber-900/60',
            'count' => 'text-amber-700 dark:text-amber-300',
        ],
        'review' => [
            'accent' => 'bg-blue-500',
            'icon' => 'bg-blue-50 text-blue-700 ring-blue-200 dark:bg-blue-950/30 dark:text-blue-300 dark:ring-blue-900/60',
            'count' => 'text-blue-700 dark:text-blue-300',
        ],
        'danger' => [
            'accent' => 'bg-red-500',
            'icon' => 'bg-red-50 text-red-700 ring-red-200 dark:bg-red-950/30 dark:text-red-300 dark:ring-red-900/60',
            'count' => 'text-red-700 dark:text-red-300',
        ],
    ];
    $classes = $toneClasses[$tone] ?? $toneClasses['neutral'];
@endphp

<x-filament::section {{ $attributes->class('h-full overflow-hidden') }}>
    <div class="relative flex min-h-40 flex-col justify-between">
        <span class="absolute -left-6 -top-6 h-24 w-1.5 rounded-full {{ $classes['accent'] }}" aria-hidden="true"></span>

        <div class="flex items-start justify-between gap-5">
            <div class="min-w-0">
                <p class="text-sm font-semibold leading-5 text-gray-600 dark:text-gray-300">{{ $title }}</p>
                <p class="mt-3 text-4xl font-semibold leading-none tracking-tight {{ $classes['count'] }}">{{ $count }}</p>
            </div>
            <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg ring-1 {{ $classes['icon'] }}" aria-hidden="true">
                <x-filament::icon :icon="$icon" class="h-5 w-5" />
            </span>
        </div>

        <p class="mt-5 text-sm leading-6 text-gray-600 dark:text-gray-300">{{ $description }}</p>
    </div>
</x-filament::section>
