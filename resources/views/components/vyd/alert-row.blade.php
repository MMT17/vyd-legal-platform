@props([
    'priority' => 'review',
    'recordType',
    'identifier',
    'secondary' => null,
    'alertTypes' => [],
    'responsible' => 'Por revisar',
    'date' => null,
    'suggestedAction' => 'Revisar asunto',
    'actionUrl' => null,
    'actionLabel' => 'Abrir registro',
])

<article {{ $attributes->class('rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-primary-300 hover:bg-gray-50/60 hover:shadow-md dark:border-white/10 dark:bg-gray-900 dark:hover:border-primary-900/60 dark:hover:bg-white/[0.03]') }}>
    <div class="grid grid-cols-1 gap-5 items-start lg:grid-cols-[180px_minmax(0,1fr)_auto]">
        <div class="flex flex-wrap items-center gap-2 lg:block lg:space-y-3">
            <x-vyd.priority-badge :priority="$priority" />
            <span class="inline-flex rounded-full border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:border-white/10 dark:bg-white/5 dark:text-gray-200">
                {{ $recordType }}
            </span>
        </div>

        <div class="min-w-0 space-y-4">
            <div>
                <h3 class="text-lg font-semibold leading-7 text-gray-950 dark:text-white">{{ $identifier }}</h3>
                @if ($secondary)
                    <p class="mt-1 max-w-3xl text-sm leading-6 text-gray-600 dark:text-gray-300">{{ $secondary }}</p>
                @endif
            </div>

            <div class="flex flex-wrap gap-1.5">
                @foreach ($alertTypes as $alertType)
                    <x-vyd.alert-badge :type="$alertType" />
                @endforeach
            </div>

            <div class="grid gap-3 rounded-lg border border-gray-100 bg-gray-50 p-3 text-sm dark:border-white/10 dark:bg-white/5 sm:grid-cols-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Responsable</p>
                    <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $responsible }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Fecha relevante</p>
                    <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $date ?: 'Sin fecha' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Acción sugerida</p>
                    <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $suggestedAction }}</p>
                </div>
            </div>
        </div>

        <div class="flex items-start justify-stretch lg:justify-end">
            @if ($actionUrl)
                <x-filament::button tag="a" :href="$actionUrl" class="w-full justify-center lg:w-auto">
                    {{ $actionLabel }}
                </x-filament::button>
            @else
                <span class="rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-500 dark:bg-white/5 dark:text-gray-400">Sin permiso para abrir este registro.</span>
            @endif
        </div>
    </div>
</article>
