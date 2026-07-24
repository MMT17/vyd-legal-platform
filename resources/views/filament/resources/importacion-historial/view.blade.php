<x-filament-panels::page>
    @include('filament.imports.partials.vyd-styles')

    @php
        $estado = match ($record->estado) {
            'completado' => 'Completada',
            'con_errores' => 'Completada con incidencias',
            'fallido' => 'Fallida',
            'procesando' => 'Procesando',
            default => str($record->estado ?? 'Sin estado')->replace('_', ' ')->title()->toString(),
        };
        $estadoTone = match ($record->estado) {
            'completado' => 'success',
            'con_errores' => 'warning',
            'fallido' => 'danger',
            'procesando' => 'info',
            default => 'neutral',
        };
    @endphp

    <div class="vyd-import space-y-6">
        <x-filament::section>
            <x-slot name="heading">Contexto de la importacion</x-slot>
            <x-slot name="description">Resumen operativo del archivo procesado.</x-slot>

            <div class="grid gap-4 md:grid-cols-3">
                <x-vyd.info-pair label="Modulo" :value="$moduleName" />
                <x-vyd.info-pair label="Archivo" :value="$record->archivo_original" />
                <x-vyd.info-pair label="Usuario" :value="$record->user?->name ?? 'Sin usuario registrado'" />
                <x-vyd.info-pair label="Fecha" :value="$record->created_at?->format('d-m-Y H:i')" />
                <x-vyd.info-pair label="Finalizada" :value="$record->completado_at?->format('d-m-Y H:i') ?? 'Sin finalizar'" />
                <x-vyd.info-pair label="Estado" :value="$estado" />
            </div>

            <div class="mt-4">
                <x-vyd.alert :tone="$estadoTone" title="{{ $estado }}">
                    @if ($record->estado === 'completado')
                        Todas las filas validas fueron procesadas sin incidencias.
                    @elseif ($record->estado === 'con_errores')
                        La importacion proceso las filas validas y dejo incidencias disponibles para revision.
                    @elseif ($record->estado === 'fallido')
                        La importacion no pudo completarse.
                    @else
                        El historial conserva el estado registrado para esta importacion.
                    @endif
                </x-vyd.alert>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Resumen</x-slot>
            <x-slot name="description">Cantidades registradas al terminar el proceso.</x-slot>

            <div class="grid gap-3 md:grid-cols-5">
                <x-vyd.metric-card label="Total" :value="$record->total_registros" tone="neutral" />
                <x-vyd.metric-card label="Creados" :value="$record->creados" tone="success" />
                <x-vyd.metric-card label="Actualizados" :value="$record->actualizados" tone="warning" />
                <x-vyd.metric-card label="Omitidos" :value="$record->duplicados" tone="neutral" />
                <x-vyd.metric-card label="Fallidos" :value="$record->errores" :tone="$record->errores > 0 ? 'danger' : 'neutral'" />
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Incidencias</x-slot>
            <x-slot name="description">Filas que no fueron procesadas o que requieren correccion.</x-slot>

            @if ($incidents === [])
                <x-vyd.empty-state
                    title="Sin incidencias registradas"
                    description="Esta importacion no tiene filas fallidas u omitidas en el reporte."
                />
            @else
                <div class="space-y-3">
                    @foreach ($incidents as $incident)
                        <div class="rounded-xl border border-[color:var(--vyd-border)] bg-[color:var(--vyd-surface)] p-4">
                            <div class="flex flex-wrap items-center gap-2 text-sm font-semibold text-gray-950 dark:text-white">
                                <span>Fila {{ $incident['fila'] ?? 'sin numero' }}</span>
                                @if (filled($incident['campo'] ?? null))
                                    <span class="text-gray-400">·</span>
                                    <span>{{ $incident['campo'] }}</span>
                                @endif
                            </div>
                            <div class="mt-2 grid gap-2 text-sm md:grid-cols-3">
                                <x-vyd.info-pair label="Valor recibido" :value="$incident['valor'] ?? 'Sin valor'" />
                                <x-vyd.info-pair label="Motivo" :value="$incident['mensaje'] ?? 'No se pudo procesar la fila.'" />
                                <x-vyd.info-pair label="Resultado" :value="($incident['estado'] ?? 'error') === 'skipped' ? 'Se omitio' : 'Requiere correccion'" />
                            </div>
                        </div>
                    @endforeach
                </div>

                @if (count($incidents) >= 50)
                    <p class="mt-3 text-sm vyd-muted">Se muestran las primeras 50 incidencias. Descarga el reporte para revisar el detalle completo.</p>
                @endif
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
