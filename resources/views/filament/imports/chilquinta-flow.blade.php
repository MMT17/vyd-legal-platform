<x-filament-panels::page>
    @include('filament.imports.partials.vyd-styles')

    @php
        $steps = [
            1 => 'Archivo',
            2 => 'Revisión',
            3 => 'Incidencias',
            4 => 'Confirmación',
            5 => 'Resultado',
        ];
        $currentStep = match ($stage) {
            'initial', 'selected', 'validating', 'validation_error' => 1,
            'review', 'validation_success' => 2,
            'incidents', 'validation_warning' => 3,
            'confirmation' => 4,
            'importing', 'success', 'partial_success', 'failed' => 5,
            default => 1,
        };
        $completedSteps = match ($stage) {
            'review', 'validation_success' => [1],
            'incidents', 'validation_warning' => [1, 2],
            'confirmation' => [1, 2, 3],
            'importing', 'success', 'partial_success', 'failed' => [1, 2, 3, 4],
            default => [],
        };
        $formatSize = fn ($bytes) => $bytes >= 1048576
            ? number_format($bytes / 1048576, 1, ',', '.').' MB'
            : number_format(max(1, $bytes) / 1024, 1, ',', '.').' KB';
        $readyRows = (int) ($analysis['ready_rows'] ?? 0);
        $invalidRows = (int) ($analysis['invalid_rows'] ?? 0);
        $updatedRows = (int) ($analysis['existing_rows'] ?? 0);
        $newRows = (int) ($analysis['new_rows'] ?? 0);
        $skippedRows = (int) ($analysis['skipped_rows'] ?? 0);
        $totalRows = (int) ($analysis['total_rows'] ?? 0);
        $previewRows = $showOnlyIssues
            ? collect($analysis['preview'] ?? [])->filter(fn ($row) => in_array($row['__status'] ?? null, ['error', 'skipped', 'warning'], true))->values()->all()
            : ($analysis['preview'] ?? []);
        $incidents = $analysis['incidents'] ?? [];
        $resultTotal = (int) ($result['total'] ?? 0);
        $resultCreated = (int) ($result['created'] ?? 0);
        $resultUpdated = (int) ($result['updated'] ?? 0);
        $resultSkipped = (int) ($result['skipped'] ?? 0);
        $resultErrors = (int) ($result['errors'] ?? 0);
        $summaryParts = array_values(array_filter([
            $newRows > 0 ? "{$newRows} se crearán" : null,
            $updatedRows > 0 ? "{$updatedRows} se actualizarán" : null,
            $skippedRows > 0 ? "{$skippedRows} se omitirán" : null,
            $invalidRows > 0 ? "{$invalidRows} requieren corrección" : null,
        ]));
        $validationSummary = $summaryParts === []
            ? "Se leyeron {$totalRows} filas y ninguna requiere corrección."
            : "Se leyeron {$totalRows} filas: ".implode(', ', $summaryParts).'.';
        $resultSummary = match (true) {
            $stage === 'failed' => 'No se pudo completar la importación. Revise el archivo y vuelva a intentarlo.',
            $resultErrors > 0 || $resultSkipped > 0 => "{$resultCreated} registros fueron creados, {$resultUpdated} actualizados y {$resultErrors} quedaron con error.",
            $resultCreated > 0 && $resultUpdated > 0 => "{$resultCreated} registros fueron creados y {$resultUpdated} actualizados correctamente.",
            $resultCreated > 0 => "{$resultCreated} registros fueron creados correctamente y no se encontraron errores.",
            $resultUpdated > 0 => "{$resultUpdated} registros fueron actualizados correctamente y no se encontraron errores.",
            default => 'La importación se completó correctamente y no se encontraron errores.',
        };
    @endphp

    <div class="vyd-ui vyd-import mx-auto max-w-6xl space-y-6">
        <div class="flex flex-col gap-4 rounded-xl border border-[color:var(--vyd-border)] bg-[color:var(--vyd-surface)] p-5 shadow-sm md:flex-row md:items-start md:justify-between">
            <div>
                <p class="text-sm font-medium text-primary-600 dark:text-primary-400">Importación CSV</p>
                <p class="mt-2 max-w-2xl text-sm leading-6 vyd-muted">
                    Carga y revisa un archivo antes de modificar los registros. La revisión no modifica información; los cambios se ejecutan solo después de confirmar.
                </p>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row md:justify-end">
                <x-filament::button color="gray" tag="a" :href="$backUrl">
                    Volver al listado
                </x-filament::button>
                <x-filament::button color="gray" tag="a" :href="$historyUrl">
                    Revisar historial
                </x-filament::button>
            </div>
        </div>

        <x-vyd.stepper :steps="$steps" :current="$currentStep" :completed="$completedSteps" />

        @if ($stage === 'initial')
            <x-filament::section>
                <x-slot name="heading">Seleccionar archivo</x-slot>
                <x-slot name="description">Selecciona el CSV que quieres revisar antes de importar.</x-slot>

                <div class="grid gap-6 lg:grid-cols-[1fr_18rem]">
                    <x-vyd.file-upload-card model="archivo" />

                    <div class="space-y-4 rounded-xl border border-[color:var(--vyd-border)] bg-[color:var(--vyd-surface-raised)] p-5">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-950 dark:text-white">¿Necesitas preparar el archivo?</h3>
                            <p class="mt-1 text-sm leading-6 vyd-muted">Descarga la plantilla oficial con los encabezados esperados.</p>
                        </div>
                        <x-filament::button color="gray" wire:click="downloadTemplate">
                            Descargar plantilla
                        </x-filament::button>
                        <div class="border-t border-[color:var(--vyd-border)] pt-3 text-xs leading-5 vyd-muted">
                            Formatos de fecha recomendados: yyyy-mm-dd, dd-mm-yyyy o dd/mm/yyyy.
                        </div>
                    </div>
                </div>
            </x-filament::section>
        @endif

        @if (in_array($stage, ['selected', 'validation_error'], true))
            <x-filament::section>
                <x-slot name="heading">Archivo seleccionado</x-slot>
                <x-slot name="description">Revisa el archivo antes de continuar.</x-slot>

                @if ($upload)
                    <div class="flex flex-col gap-4 rounded-xl border border-[color:var(--vyd-border)] bg-[color:var(--vyd-surface-raised)] p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-700 ring-1 ring-amber-200 dark:bg-amber-950/30 dark:text-amber-300 dark:ring-amber-900/70" aria-hidden="true">
                                <x-filament::icon icon="heroicon-o-document-text" class="h-6 w-6" />
                            </span>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-[color:var(--vyd-text)]">{{ $upload['original_name'] }}</p>
                                <p class="mt-0.5 text-sm vyd-muted">{{ $formatSize($upload['size']) }} · {{ $moduleName }}</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 sm:justify-end">
                            <label class="vyd-file-action">
                                Reemplazar archivo
                                <input type="file" class="vyd-native-file" wire:model="archivo" accept=".csv,text/csv" aria-label="Reemplazar archivo">
                            </label>
                            <x-filament::button color="gray" wire:click="removeFile">
                                Quitar archivo
                            </x-filament::button>
                        </div>
                    </div>
                @endif

                @if ($analysisError)
                    <div class="mt-4">
                        <x-vyd.alert tone="danger" title="No podemos revisar este archivo">
                            {{ $analysisError }}
                        </x-vyd.alert>
                    </div>
                @endif

                <div class="mt-5 flex justify-end">
                    <x-filament::button wire:click="reviewFile" :disabled="$stage === 'validation_error'">
                        Revisar archivo
                    </x-filament::button>
                </div>
            </x-filament::section>
        @endif

        @if ($stage === 'validating')
            <x-filament::section>
                <x-slot name="heading">Revisando archivo</x-slot>
                <div class="flex items-center gap-3 rounded-xl border border-blue-200 bg-blue-50 p-4 text-blue-900 dark:border-blue-900/60 dark:bg-blue-950/30 dark:text-blue-100" role="status" aria-live="polite">
                    <x-filament::icon icon="heroicon-o-arrow-path" class="h-5 w-5 animate-spin" />
                    <p class="text-sm">Estamos leyendo el archivo para preparar la vista previa. Aún no se modifica información.</p>
                </div>
            </x-filament::section>
        @endif

        <div
            id="import-stage-content"
            x-data
            x-effect="
                $nextTick(() => {
                    const target = $el.querySelector('[data-stage-heading]');
                    target?.focus({ preventScroll: true });

                    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                    $el.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'start' });
                })
            "
            wire:key="import-stage-{{ $stage }}"
        >
        @if ($stage === 'review' && $analysis)
            <x-filament::section>
                <x-slot name="heading">
                    <span tabindex="-1" data-stage-heading>Vista previa</span>
                </x-slot>
                <x-slot name="description">Muestra de las primeras filas interpretadas. El archivo completo se procesará al importar.</x-slot>

                <div class="vyd-metrics-grid vyd-metrics-grid--review">
                    <x-vyd.metric-card label="Filas listas para importar" :value="$readyRows" tone="success" icon="heroicon-o-check-circle" />
                    <x-vyd.metric-card label="Filas que requieren corrección" :value="$invalidRows" :tone="$invalidRows > 0 ? 'danger' : 'neutral'" icon="heroicon-o-exclamation-triangle" />
                    <x-vyd.metric-card label="Registros que se actualizarán" :value="$updatedRows" :tone="$updatedRows > 0 ? 'warning' : 'neutral'" icon="heroicon-o-arrow-path" />
                </div>

                <p class="mt-4 rounded-lg bg-gray-50 px-3 py-2 text-sm leading-6 vyd-muted dark:bg-white/5">
                    {{ $validationSummary }}
                </p>

                <div class="mt-5">
                    <x-vyd.preview-table
                        :columns="$visibleColumns"
                        :rows="$previewRows"
                        status-column="__status"
                        empty-message="No hay filas para mostrar con el filtro actual."
                    />
                </div>

                <div class="mt-5 flex justify-end">
                    <x-filament::button wire:click="goToIncidents">
                        Continuar a incidencias
                    </x-filament::button>
                </div>
            </x-filament::section>

        @elseif ($stage === 'incidents' && $analysis)
            <x-filament::section>
                <x-slot name="heading">
                    <span tabindex="-1" data-stage-heading>Incidencias</span>
                </x-slot>
                <x-slot name="description">Revisa las filas que quedarán fuera o que necesitan corrección antes de importar.</x-slot>

                @if ($incidents === [])
                    <x-vyd.empty-state
                        title="Sin incidencias"
                        description="Todas las filas revisadas están listas para importarse."
                        icon="heroicon-o-check-circle"
                        tone="success"
                        compact
                    />
                @else
                    <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:flex-wrap">
                        <x-filament::button color="gray" wire:click="toggleIssuesOnly">
                            {{ $showOnlyIssues ? 'Ver muestra general' : 'Ver solo filas con problemas' }}
                        </x-filament::button>
                        <x-filament::button color="gray" wire:click="downloadAnalysisErrors">
                            Descargar reporte de errores
                        </x-filament::button>
                    </div>

                    <div class="space-y-3">
                        @foreach ($incidents as $incident)
                            <div class="rounded-xl border border-[color:var(--vyd-border)] bg-[color:var(--vyd-surface)] p-4 shadow-sm">
                                <div class="flex flex-wrap items-center gap-2 text-sm font-semibold text-gray-950 dark:text-white">
                                    <x-vyd.status-badge variant="error" />
                                    <span>Fila {{ $incident['row'] }}</span>
                                    <span class="text-gray-400">·</span>
                                    <span>{{ $incident['field'] }}</span>
                                </div>
                                <div class="mt-2 grid gap-2 text-sm md:grid-cols-3">
                                    <x-vyd.info-pair label="Valor recibido" :value="$incident['value'] ?: 'Sin valor'" />
                                    <x-vyd.info-pair label="Motivo" :value="$incident['message']" />
                                    <x-vyd.info-pair label="Cómo corregir" :value="$incident['correction']" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-5 flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-wrap gap-2">
                        <label class="vyd-file-action">
                            Reemplazar archivo
                            <input type="file" class="vyd-native-file" wire:model="archivo" accept=".csv,text/csv" aria-label="Reemplazar archivo">
                        </label>
                        <x-filament::button color="gray" wire:click="removeFile">
                            Quitar archivo
                        </x-filament::button>
                    </div>
                    <x-filament::button wire:click="goToConfirmation" :disabled="! $this->canMoveToConfirmation()">
                        Continuar a confirmación
                    </x-filament::button>
                </div>
            </x-filament::section>

        @elseif ($stage === 'confirmation' && $analysis && $upload)
            <x-filament::section>
                <x-slot name="heading">
                    <span tabindex="-1" data-stage-heading>Confirmar importación</span>
                </x-slot>
                <x-slot name="description">Esta acción modificará información de {{ strtolower($moduleName) }}.</x-slot>

                <div class="vyd-summary-grid">
                    <x-vyd.info-pair label="Módulo" :value="$moduleName" />
                    <x-vyd.info-pair label="Archivo" :value="$upload['original_name']" />
                    <x-vyd.info-pair label="Filas que se procesarán" :value="$readyRows" />
                    <x-vyd.info-pair label="Filas que se crearán" :value="$newRows" />
                    <x-vyd.info-pair label="Filas que se actualizarán" :value="$updatedRows" />
                    <x-vyd.info-pair label="Filas omitidas" :value="$skippedRows" />
                    <x-vyd.info-pair label="Filas con error" :value="$invalidRows" />
                </div>

                <div class="mt-4">
                    <x-vyd.alert :tone="$invalidRows > 0 || $skippedRows > 0 ? 'warning' : 'info'" title="Antes de importar">
                        Se procesarán solamente las filas válidas. Las filas omitidas o con error quedarán fuera y podrán revisarse en el reporte.
                    </x-vyd.alert>
                </div>

                <div class="mt-5 flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
                    <x-filament::button color="gray" wire:click="backToReview" :disabled="$importing">
                        Volver a revisar
                    </x-filament::button>
                    <x-filament::button wire:click="importRecords" wire:loading.attr="disabled" :disabled="$importing">
                        Importar registros
                    </x-filament::button>
                </div>
            </x-filament::section>

        @elseif ($stage === 'importing')
            <x-filament::section>
                <x-slot name="heading">
                    <span tabindex="-1" data-stage-heading>Importando registros</span>
                </x-slot>
                <div class="flex items-center gap-3 rounded-xl border border-blue-200 bg-blue-50 p-4 text-blue-900 dark:border-blue-900/60 dark:bg-blue-950/30 dark:text-blue-100" role="status" aria-live="polite">
                    <x-filament::icon icon="heroicon-o-arrow-path" class="h-5 w-5 animate-spin" />
                    <p class="text-sm">El archivo se está procesando. Mantendremos visible el resultado al finalizar.</p>
                </div>
            </x-filament::section>

        @elseif (in_array($stage, ['success', 'partial_success', 'failed'], true))
            <x-filament::section>
                <x-slot name="heading">
                    <span tabindex="-1" data-stage-heading>
                        @if ($stage === 'success')
                            Importación exitosa
                        @elseif ($stage === 'partial_success')
                            Importación parcial
                        @else
                            Importación fallida
                        @endif
                    </span>
                </x-slot>

                <div class="mb-5 flex items-start gap-4 rounded-xl border border-[color:var(--vyd-border)] bg-[color:var(--vyd-surface-raised)] p-5">
                    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg {{ $stage === 'failed' ? 'bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-300' : 'bg-green-50 text-green-700 dark:bg-green-950/40 dark:text-green-300' }}">
                        <x-filament::icon :icon="$stage === 'failed' ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle'" class="h-6 w-6" />
                    </span>
                    <div>
                        <h3 class="text-base font-semibold text-[color:var(--vyd-text)]">
                            {{ $stage === 'failed' ? 'Importación no completada' : 'Importación completada' }}
                        </h3>
                        <p class="mt-1 text-sm leading-6 vyd-muted">{{ $resultSummary }}</p>
                    </div>
                </div>

                @if ($stage === 'failed')
                    <x-vyd.alert tone="danger" title="No se pudo importar el archivo">
                        No se realizaron cambios desde esta pantalla. Revise el archivo y vuelva a intentarlo.
                    </x-vyd.alert>
                @else
                    <div class="vyd-metrics-grid vyd-metrics-grid--result">
                        <x-vyd.metric-card label="Creados" :value="$resultCreated" tone="success" icon="heroicon-o-plus-circle" />
                        <x-vyd.metric-card label="Actualizados" :value="$resultUpdated" tone="warning" icon="heroicon-o-arrow-path" />
                        <x-vyd.metric-card label="Omitidos" :value="$resultSkipped" tone="neutral" icon="heroicon-o-minus-circle" />
                        <x-vyd.metric-card label="Fallidos" :value="$resultErrors" :tone="$resultErrors > 0 ? 'danger' : 'neutral'" icon="heroicon-o-exclamation-triangle" />
                        <x-vyd.metric-card label="Total revisado" :value="$resultTotal" tone="neutral" icon="heroicon-o-document-check" />
                    </div>

                    <div class="vyd-summary-grid vyd-summary-grid--compact">
                        <x-vyd.info-pair label="Módulo" :value="$moduleName" />
                        <x-vyd.info-pair label="Archivo" :value="$upload['original_name'] ?? 'Archivo CSV'" />
                        <x-vyd.info-pair label="Usuario" :value="auth()->user()?->name ?? 'Usuario actual'" />
                        <x-vyd.info-pair label="Fecha y hora" :value="now()->format('d-m-Y H:i')" />
                    </div>

                    @if ($resultErrors > 0 || $resultSkipped > 0)
                        <div class="mt-4">
                            <x-vyd.alert tone="warning" title="Hay incidencias por revisar">
                                La importación procesó las filas válidas. Revisa o descarga el reporte para corregir las filas pendientes.
                            </x-vyd.alert>
                        </div>
                    @endif
                @endif

                <div class="mt-5 flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end">
                    <x-filament::button wire:click="startNewImport">
                        Realizar otra importación
                    </x-filament::button>
                    <x-filament::button color="gray" tag="a" :href="$backUrl">
                        Ver registros importados
                    </x-filament::button>
                    <x-filament::button color="gray" tag="a" :href="$historyUrl">
                        Revisar historial
                    </x-filament::button>
                    @if ($detailUrl)
                        <x-filament::button color="gray" tag="a" :href="$detailUrl">
                            Ver detalle
                        </x-filament::button>
                    @endif
                    @if (($result['error_report_path'] ?? null) && ($resultErrors > 0 || $resultSkipped > 0))
                        <x-filament::button color="gray" wire:click="downloadResultErrors">
                            Descargar errores
                        </x-filament::button>
                    @endif
                </div>
            </x-filament::section>
        @endif
        </div>
    </div>
</x-filament-panels::page>
