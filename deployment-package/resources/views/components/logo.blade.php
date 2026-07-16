@props([
    'href' => null,
    'size' => 'md',
    'variant' => 'horizontal',
])

<x-branding.logo
    :href="$href"
    :variant="$variant"
    {{ $attributes->class(['vyd-logo', 'vyd-logo--' . $size]) }}
/>
