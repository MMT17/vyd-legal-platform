@props(['label', 'value'])

<div {{ $attributes->class('vyd-info-pair') }}>
    <dt class="vyd-info-pair__label">{{ $label }}</dt>
    <dd class="vyd-info-pair__value">
        {!! filled($value) ? e($value) : '&mdash;' !!}
    </dd>
</div>
