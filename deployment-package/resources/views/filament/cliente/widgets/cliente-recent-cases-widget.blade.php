<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Casos recientes
        </x-slot>

        <x-slot name="description">
            Últimos convenios y querellas registrados.
        </x-slot>

        <div class="divide-y divide-gray-100 dark:divide-white/10">
            @forelse ($cases as $case)
                <div class="grid gap-2 py-3 text-sm md:grid-cols-[7rem_1fr_10rem_8rem] md:items-center">
                    <span class="font-medium text-gray-700 dark:text-gray-200">{{ $case['tipo'] }}</span>
                    <span class="truncate text-gray-950 dark:text-white">{{ $case['numero'] }}</span>
                    <span class="text-gray-600 dark:text-gray-300">{{ $case['estado'] }}</span>
                    <span class="text-gray-500 dark:text-gray-400">
                        {{ $case['fecha'] ? \Illuminate\Support\Carbon::parse($case['fecha'])->format('d-m-Y') : 'Sin fecha' }}
                    </span>
                </div>
            @empty
                <div class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                    No hay casos recientes disponibles.
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
