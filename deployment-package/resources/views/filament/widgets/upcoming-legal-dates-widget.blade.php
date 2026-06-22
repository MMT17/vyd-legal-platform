<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Próximas fechas
        </x-slot>

        <x-slot name="description">
            Hitos futuros de convenios, querellas, procesos y contactos.
        </x-slot>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-white/10 dark:bg-gray-900">
            <div class="grid grid-cols-[7rem_1fr_8rem_7rem] gap-3 border-b border-gray-200 bg-gray-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:border-white/10 dark:bg-white/5 dark:text-gray-400">
                <span>Tipo</span>
                <span>Nombre</span>
                <span>Fecha</span>
                <span>Días</span>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-white/10">
                @forelse ($records as $record)
                    <div class="grid grid-cols-[7rem_1fr_8rem_7rem] gap-3 px-4 py-3 text-sm">
                        <span class="font-medium text-gray-700 dark:text-gray-200">{{ $record['type'] }}</span>
                        <span class="truncate text-gray-950 dark:text-white">{{ $record['title'] }}</span>
                        <span class="text-gray-600 dark:text-gray-300">{{ $record['date']->format('d-m-Y') }}</span>
                        <span class="font-medium text-primary-600 dark:text-primary-400">
                            {{ $record['days_remaining'] === 0 ? 'Hoy' : $record['days_remaining'] }}
                        </span>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        No hay fechas futuras disponibles.
                    </div>
                @endforelse
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
