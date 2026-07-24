@props(['processed' => 0, 'created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0])

<div {{ $attributes->class('vyd-grid-metrics') }}>
    <x-vyd.metric-card label="Procesados" :value="$processed" icon="heroicon-o-clipboard-document-list" />
    <x-vyd.metric-card label="Creados" :value="$created" tone="success" icon="heroicon-o-check-circle" />
    <x-vyd.metric-card label="Actualizados" :value="$updated" tone="gold" icon="heroicon-o-arrow-path" />
    <x-vyd.metric-card label="Omitidos" :value="$skipped" tone="neutral" icon="heroicon-o-minus-circle" />
    <x-vyd.metric-card label="Errores" :value="$errors" tone="danger" icon="heroicon-o-x-circle" />
</div>
