@props([
    'variant' => 'pending',
])

@php
    $styles = [
        'created' => 'border-green-200 bg-green-50 text-green-700 dark:border-green-900/60 dark:bg-green-950/30 dark:text-green-300',
        'updated' => 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-900/60 dark:bg-amber-950/30 dark:text-amber-300',
        'skipped' => 'border-gray-200 bg-gray-50 text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300',
        'error' => 'border-red-200 bg-red-50 text-red-700 dark:border-red-900/60 dark:bg-red-950/30 dark:text-red-300',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-900/60 dark:bg-amber-950/30 dark:text-amber-300',
        'pending' => 'border-gray-200 bg-gray-50 text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300',
        'processing' => 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-900/60 dark:bg-blue-950/30 dark:text-blue-300',
        'completed' => 'border-green-200 bg-green-50 text-green-700 dark:border-green-900/60 dark:bg-green-950/30 dark:text-green-300',
    ];
    $labels = [
        'created' => 'Se creará',
        'updated' => 'Se actualizará',
        'skipped' => 'Se omitirá',
        'error' => 'Requiere corrección',
        'warning' => 'Advertencia',
        'pending' => 'Pendiente',
        'processing' => 'Procesando',
        'completed' => 'Completada',
    ];
    $icons = [
        'created' => 'heroicon-m-plus',
        'updated' => 'heroicon-m-arrow-path',
        'skipped' => 'heroicon-m-minus',
        'error' => 'heroicon-m-exclamation-triangle',
        'warning' => 'heroicon-m-exclamation-circle',
        'pending' => 'heroicon-m-clock',
        'processing' => 'heroicon-m-arrow-path',
        'completed' => 'heroicon-m-check',
    ];
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-medium leading-none', $styles[$variant] ?? $styles['pending']]) }}>
    <x-filament::icon :icon="$icons[$variant] ?? $icons['pending']" class="h-3.5 w-3.5" aria-hidden="true" />
    {{ $slot->isEmpty() ? ($labels[$variant] ?? $variant) : $slot }}
</span>
