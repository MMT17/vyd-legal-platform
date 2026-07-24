@props([
    'priority' => 'review',
])

@php
    $isAction = $priority === 'action';
@endphp

<span {{ $attributes->class([
    'inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-semibold shadow-sm',
    $isAction
        ? 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-900/60 dark:bg-amber-950/30 dark:text-amber-200'
        : 'border-blue-200 bg-blue-50 text-blue-800 dark:border-blue-900/60 dark:bg-blue-950/30 dark:text-blue-200',
]) }}>
    <x-filament::icon :icon="$isAction ? 'heroicon-m-bolt' : 'heroicon-m-eye'" class="h-3.5 w-3.5" aria-hidden="true" />
    {{ $isAction ? 'Requiere acción' : 'Requiere revisión' }}
</span>
