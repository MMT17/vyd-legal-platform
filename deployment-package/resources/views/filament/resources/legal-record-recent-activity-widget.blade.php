<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ $title }}
        </x-slot>

        <x-slot name="description">
            {{ $description }}
        </x-slot>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-white/10 dark:bg-gray-900">
            <div class="hidden grid-cols-[10rem_12rem_9rem_1fr] gap-3 border-b border-gray-200 bg-gray-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:border-white/10 dark:bg-white/5 dark:text-gray-400 md:grid">
                <span>Fecha</span>
                <span>Usuario</span>
                <span>Acción</span>
                <span>Descripción</span>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-white/10">
                @forelse ($records as $activity)
                    <div class="grid gap-1 px-4 py-3 text-sm md:grid-cols-[10rem_12rem_9rem_1fr] md:gap-3">
                        <span class="text-gray-500 dark:text-gray-400">
                            {{ $activity->created_at?->format('d-m-Y H:i') }}
                        </span>
                        <span class="truncate text-gray-700 dark:text-gray-200">
                            {{ $activity->causer?->name ?: 'Sistema' }}
                        </span>
                        <span class="font-medium text-gray-700 dark:text-gray-200">
                            @switch($activity->event)
                                @case('created')
                                    Creado
                                    @break
                                @case('updated')
                                    Actualizado
                                    @break
                                @case('deleted')
                                    Eliminado
                                    @break
                                @case('downloaded')
                                    Descargado
                                    @break
                                @case('imported')
                                    Importado
                                    @break
                                @default
                                    {{ $activity->event ?: 'Actividad' }}
                            @endswitch
                        </span>
                        <span class="text-gray-950 dark:text-white">
                            {{ $activity->description }}
                        </span>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        No hay actividad reciente disponible.
                    </div>
                @endforelse
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
