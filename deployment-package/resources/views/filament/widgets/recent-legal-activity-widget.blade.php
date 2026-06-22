<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Actividad reciente
        </x-slot>

        <x-slot name="description">
            Últimos 10 registros creados en la plataforma.
        </x-slot>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-white/10 dark:bg-gray-900">
            <div class="hidden grid-cols-[7rem_1fr_8rem_9rem] gap-3 border-b border-gray-200 bg-gray-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:border-white/10 dark:bg-white/5 dark:text-gray-400 md:grid">
                <span>Tipo</span>
                <span>Nombre</span>
                <span>Estado</span>
                <span>Creación</span>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-white/10">
                @forelse ($records as $record)
                    <div class="grid gap-1 px-4 py-3 text-sm md:grid-cols-[7rem_1fr_8rem_9rem] md:gap-3">
                        <span class="font-medium text-gray-700 dark:text-gray-200">{{ $record['type'] }}</span>
                        <span class="truncate text-gray-950 dark:text-white">{{ $record['title'] }}</span>
                        <span class="truncate text-gray-600 dark:text-gray-300">{{ $record['state'] ?: 'Sin estado' }}</span>
                        <span class="text-gray-500 dark:text-gray-400">
                            {{ $record['created_at']?->format('d-m-Y H:i') }}
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
