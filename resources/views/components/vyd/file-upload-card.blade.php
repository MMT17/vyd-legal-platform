@props([
    'model' => 'archivo',
    'title' => 'Arrastra tu archivo CSV aquí',
    'buttonLabel' => 'Seleccionar archivo',
    'helper' => 'Solo archivos CSV',
])

<label
    x-data="{ dragging: false }"
    x-on:dragenter.prevent="dragging = true"
    x-on:dragover.prevent="dragging = true"
    x-on:dragleave.prevent="dragging = false"
    x-on:drop.prevent="
        dragging = false;
        const files = $event.dataTransfer?.files;
        if (files && files.length) {
            $refs.input.files = files;
            $refs.input.dispatchEvent(new Event('change', { bubbles: true }));
        }
    "
    x-bind:class="{ 'is-dragging': dragging }"
    {{ $attributes->class('vyd-upload') }}
>
    <input
        x-ref="input"
        type="file"
        class="vyd-upload__input"
        wire:model="{{ $model }}"
        accept=".csv,text/csv"
        aria-label="{{ $buttonLabel }}"
    >

    <span class="vyd-upload__icon" aria-hidden="true">
        <x-filament::icon icon="heroicon-o-arrow-up-tray" class="h-7 w-7" />
    </span>

    <span class="vyd-upload__title">{{ $title }}</span>
    <span class="vyd-upload__separator">o</span>
    <span class="vyd-upload__button">
        {{ $buttonLabel }}
    </span>
    <span class="vyd-upload__helper">{{ $helper }}</span>
</label>
