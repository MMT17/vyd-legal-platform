<x-filament-panels::page>
    @php
        $recordTypeFor = fn (string $module): string => match ($module) {
            'convenio' => 'Convenio',
            'querella' => 'Querella',
            'proceso' => 'Proceso',
            'contacto' => 'Contacto',
            default => str($module)->headline()->toString(),
        };

        $actionLabelFor = fn (string $module): string => match ($module) {
            'convenio' => 'Abrir convenio',
            'querella' => 'Abrir querella',
            'proceso' => 'Abrir proceso',
            'contacto' => 'Abrir contacto',
            default => 'Abrir registro',
        };

        $priorityFor = fn (array $alertTypes): string => collect($alertTypes)
            ->intersect(['without_lawyer', 'without_date', 'pending_contact'])
            ->isNotEmpty() ? 'action' : 'review';

        $suggestedActionFor = function (array $alertTypes): string {
            if (in_array('without_lawyer', $alertTypes, true)) {
                return 'Asignar responsable';
            }

            if (in_array('without_date', $alertTypes, true)) {
                return 'Registrar fecha';
            }

            if (in_array('pending_contact', $alertTypes, true)) {
                return 'Revisar contacto';
            }

            if (in_array('without_documents', $alertTypes, true)) {
                return 'Revisar documentos';
            }

            return 'Revisar actividad';
        };
    @endphp

    <div class="mx-auto max-w-7xl space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-sm font-medium text-primary-600 dark:text-primary-400">Bandeja operativa</p>
                    <h2 class="mt-1 text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
                        Asuntos que requieren atención
                    </h2>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Revisa los asuntos detectados automáticamente y abre el registro correspondiente para avanzar con la gestión.
                    </p>
                </div>

                <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600 dark:border-white/10 dark:bg-white/5 dark:text-gray-300">
                    <span class="block text-2xl font-semibold leading-none text-gray-950 dark:text-white">{{ $rows->count() }}</span>
                    <span class="mt-1 block">asuntos visibles</span>
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($summaryCards as $card)
                <x-vyd.alert-summary-card
                    :title="$card['title']"
                    :count="$card['count']"
                    :description="$card['description']"
                    :icon="$card['icon']"
                    :tone="$card['tone']"
                />
            @endforeach
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="flex flex-wrap gap-2">
                @foreach ($filters as $filter)
                    <button
                        type="button"
                        wire:click="setFilter('{{ $filter['key'] }}')"
                        @class([
                        'inline-flex items-center rounded-full border px-3 py-1.5 text-sm font-medium',
                        'border-primary-200 bg-primary-50 text-primary-700 dark:border-primary-900/60 dark:bg-primary-950/30 dark:text-primary-300' => $activeFilter === $filter['key'],
                        'border-gray-200 bg-gray-50 text-gray-700 dark:border-white/10 dark:bg-white/5 dark:text-gray-200' => $activeFilter !== $filter['key'],
                    ])
                    >
                        {{ $filter['label'] }}
                    </button>
                @endforeach
            </div>
        </div>

        <x-filament::section>
            <x-slot name="heading">Bandeja principal</x-slot>
            <x-slot name="description">
                Prioriza los asuntos por nivel operativo y revisa la acción sugerida para cada registro.
            </x-slot>

            @if ($rows->isEmpty())
                <x-vyd.empty-state
                    :title="$allRowsCount === 0 ? 'No hay asuntos que requieran atención' : 'No hay asuntos para este filtro'"
                    :description="$allRowsCount === 0 ? 'Cuando el sistema detecte alertas operativas, aparecerán en esta bandeja.' : 'Prueba seleccionar otro filtro o volver a Todos.'"
                    icon="heroicon-o-check-circle"
                    tone="success"
                />
            @else
                <div class="space-y-3">
                    @foreach ($rows as $row)
                        @php
                            $alertTypes = $row['alert_types'];
                        @endphp

                        <x-vyd.alert-row
                            wire:key="operational-alert-{{ $row['record_key'] }}"
                            :priority="$row['priority'] ?? $priorityFor($alertTypes)"
                            :record-type="$recordTypeFor($row['module'])"
                            :identifier="$row['identifier']"
                            :secondary="$row['secondary']"
                            :alert-types="$alertTypes"
                            :responsible="$row['responsible'] ?: (in_array('without_lawyer', $alertTypes, true) ? 'Sin asignar' : 'Por revisar')"
                            :date="$row['relevant_date']?->format('d-m-Y H:i')"
                            :suggested-action="$row['suggested_action'] ?? $suggestedActionFor($alertTypes)"
                            :action-url="$row['url']"
                            :action-label="$actionLabelFor($row['module'])"
                        />
                    @endforeach
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
