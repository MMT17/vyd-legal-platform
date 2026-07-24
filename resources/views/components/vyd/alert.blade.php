@props([
    'title',
    'description' => null,
    'tone' => 'info',
    'icon' => null,
    'compact' => false,
    'actionLabel' => null,
    'actionUrl' => null,
])

@php
    $toneMap = [
        'info' => ['class' => 'border-blue-200 bg-blue-50 text-blue-900 dark:border-blue-900/60 dark:bg-blue-950/30 dark:text-blue-100', 'icon' => 'heroicon-o-information-circle'],
        'success' => ['class' => 'border-green-200 bg-green-50 text-green-900 dark:border-green-900/60 dark:bg-green-950/30 dark:text-green-100', 'icon' => 'heroicon-o-check-circle'],
        'warning' => ['class' => 'border-amber-200 bg-amber-50 text-amber-900 dark:border-amber-900/60 dark:bg-amber-950/30 dark:text-amber-100', 'icon' => 'heroicon-o-exclamation-triangle'],
        'danger' => ['class' => 'border-red-200 bg-red-50 text-red-900 dark:border-red-900/60 dark:bg-red-950/30 dark:text-red-100', 'icon' => 'heroicon-o-x-circle'],
        'neutral' => ['class' => 'border-gray-200 bg-gray-50 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100', 'icon' => 'heroicon-o-information-circle'],
    ];
    $toneData = $toneMap[$tone] ?? $toneMap['info'];
@endphp

<div {{ $attributes->class(['rounded-xl border p-4', $compact ? 'p-3' : 'p-4', $toneData['class']]) }} role="status">
    <div class="flex gap-3">
        <x-filament::icon :icon="$icon ?: $toneData['icon']" class="mt-0.5 h-5 w-5 shrink-0" />
        <div class="min-w-0">
            <p class="text-sm font-semibold">{{ $title }}</p>
            @if ($description)
                <p class="mt-1 text-sm opacity-85">{{ $description }}</p>
            @endif
            @if (! $slot->isEmpty())
                <div class="mt-2 text-sm">{{ $slot }}</div>
            @endif
            @if ($actionLabel && $actionUrl)
                <a href="{{ $actionUrl }}" class="vyd-focusable mt-3 inline-flex rounded-md text-sm font-semibold underline underline-offset-4">
                    {{ $actionLabel }}
                </a>
            @endif
        </div>
    </div>
</div>
