<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Documentos recientes
        </x-slot>

        <x-slot name="description">
            Últimos documentos disponibles para revisión.
        </x-slot>

        <div class="divide-y divide-gray-100 dark:divide-white/10">
            @forelse ($documents as $document)
                <div class="grid gap-2 py-3 text-sm md:grid-cols-[1fr_9rem_8rem_7rem] md:items-center">
                    <div class="min-w-0">
                        <div class="truncate font-medium text-gray-950 dark:text-white">
                            {{ $document->nombre ?: 'Sin nombre' }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $document->tipo_documento ?: 'Sin tipo' }}
                        </div>
                    </div>
                    <span class="text-gray-600 dark:text-gray-300">
                        {{ $document->fecha?->format('d-m-Y') ?: 'Sin fecha' }}
                    </span>
                    <span class="text-gray-500 dark:text-gray-400">
                        {{ $document->created_at?->format('d-m-Y') }}
                    </span>
                    <span>
                        @if ($document->archivo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($document->archivo_path))
                            <a
                                href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($document->archivo_path) }}"
                                target="_blank"
                                class="font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400"
                            >
                                Descargar
                            </a>
                        @else
                            <span class="text-gray-400">Sin archivo</span>
                        @endif
                    </span>
                </div>
            @empty
                <div class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                    No hay documentos recientes disponibles.
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
