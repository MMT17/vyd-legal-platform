<x-filament-panels::page>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($alerts as $alert)
            <div @class([
                'rounded-lg border bg-white p-4 shadow-sm dark:bg-gray-900',
                'border-danger-200 dark:border-danger-900' => $alert['color'] === 'danger',
                'border-warning-200 dark:border-warning-900' => $alert['color'] === 'warning',
                'border-info-200 dark:border-info-900' => $alert['color'] === 'info',
            ])>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    {{ $alert['type'] }}
                </div>
                <div class="mt-2 text-3xl font-semibold text-gray-950 dark:text-white">
                    {{ $alert['count'] }}
                </div>
                <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                    {{ $alert['description'] }}
                </div>
            </div>
        @endforeach
    </div>

    <div class="space-y-6">
        @foreach ($sections as $section => $records)
            <x-filament::section>
                <x-slot name="heading">
                    {{ $section }}
                </x-slot>

                <x-slot name="description">
                    Alertas activas de {{ strtolower($section) }}.
                </x-slot>

                <div class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-white/10 dark:bg-gray-900">
                    <div class="hidden grid-cols-[13rem_1fr_1.4fr_10rem_5rem] gap-3 border-b border-gray-200 bg-gray-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:border-white/10 dark:bg-white/5 dark:text-gray-400 md:grid">
                        <span>Tipo de alerta</span>
                        <span>Registro relacionado</span>
                        <span>Descripción</span>
                        <span>Fecha relevante</span>
                        <span>Acción</span>
                    </div>

                    <div class="divide-y divide-gray-100 dark:divide-white/10">
                        @forelse ($records as $record)
                            <div class="grid gap-1 px-4 py-3 text-sm md:grid-cols-[13rem_1fr_1.4fr_10rem_5rem] md:gap-3">
                                <span @class([
                                    'font-medium',
                                    'text-danger-700 dark:text-danger-300' => $record['color'] === 'danger',
                                    'text-warning-700 dark:text-warning-300' => $record['color'] === 'warning',
                                    'text-info-700 dark:text-info-300' => $record['color'] === 'info',
                                ])>
                                    {{ $record['type'] }}
                                </span>
                                <span class="truncate text-gray-950 dark:text-white">
                                    {{ $record['record'] }}
                                </span>
                                <span class="text-gray-600 dark:text-gray-300">
                                    {{ $record['description'] }}
                                </span>
                                <span class="text-gray-500 dark:text-gray-400">
                                    {{ $record['date']?->format('d-m-Y H:i') ?: 'Sin fecha' }}
                                </span>
                                <span>
                                    @if ($record['url'])
                                        <a href="{{ $record['url'] }}" class="font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400">
                                            Ver
                                        </a>
                                    @else
                                        <span class="text-gray-400">Sin acción</span>
                                    @endif
                                </span>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                No hay alertas activas en esta sección.
                            </div>
                        @endforelse
                    </div>
                </div>
            </x-filament::section>
        @endforeach
    </div>
</x-filament-panels::page>
