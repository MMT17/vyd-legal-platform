@props([
    'label',
    'value',
    'description' => null,
    'icon' => null,
    'tone' => 'neutral',
    'percentage' => null,
])

@php
    $toneClass = in_array($tone, ['neutral', 'info', 'success', 'warning', 'danger', 'gold'], true)
        ? $tone
        : 'neutral';
@endphp

<div {{ $attributes->class('vyd-card vyd-metric') }}>
    <div class="vyd-metric__content">
        @if ($icon)
            <span class="vyd-metric__icon vyd-metric__icon--{{ $toneClass }}">
                <x-filament::icon :icon="$icon" />
            </span>
        @endif
        <div class="vyd-metric__body">
            <p class="vyd-metric__value">{{ $value }}</p>
            <p class="vyd-metric__label">{{ $label }}</p>
        </div>
    </div>
    @if ($description || $percentage !== null)
        <p class="vyd-metric__description">
            @if ($percentage !== null)
                <span>{{ $percentage }}%</span>
            @endif
            {{ $description }}
        </p>
    @endif
</div>
