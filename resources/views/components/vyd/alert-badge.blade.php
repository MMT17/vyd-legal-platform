@props([
    'type' => 'activity',
])

@php
    $labels = [
        'without_lawyer' => 'Sin abogado',
        'without_documents' => 'Sin documentos',
        'without_activity' => 'Sin actividad',
        'without_date' => 'Sin fecha',
        'pending_contact' => 'Contacto pendiente',
        'lawyer' => 'Sin abogado',
        'documents' => 'Sin documentos',
        'activity' => 'Sin actividad',
        'date' => 'Sin fecha',
        'contact' => 'Contacto pendiente',
    ];

    $icons = [
        'without_lawyer' => 'heroicon-m-user-minus',
        'without_documents' => 'heroicon-m-document-minus',
        'without_activity' => 'heroicon-m-clock',
        'without_date' => 'heroicon-m-calendar-days',
        'pending_contact' => 'heroicon-m-chat-bubble-left-ellipsis',
        'lawyer' => 'heroicon-m-user-minus',
        'documents' => 'heroicon-m-document-minus',
        'activity' => 'heroicon-m-clock',
        'date' => 'heroicon-m-calendar-days',
        'contact' => 'heroicon-m-chat-bubble-left-ellipsis',
    ];
@endphp

<span {{ $attributes->class('inline-flex items-center gap-1 rounded-full border border-gray-300 bg-gray-100 px-2.5 py-1 text-xs font-medium leading-5 text-gray-700 dark:border-white/10 dark:bg-white/10 dark:text-gray-200') }}>
    <x-filament::icon :icon="$icons[$type] ?? 'heroicon-m-bell-alert'" class="h-3 w-3 text-gray-500 dark:text-gray-400" aria-hidden="true" />
    {{ $labels[$type] ?? $slot }}
</span>
