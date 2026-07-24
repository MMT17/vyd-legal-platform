@props(['status' => 'pending', 'title', 'description' => null])

<div {{ $attributes->class('flex items-start justify-between gap-3 rounded-xl border border-[color:var(--vyd-border)] p-3') }}>
    <div>
        <p class="text-sm font-medium text-[color:var(--vyd-text)]">{{ $title }}</p>
        @if ($description)
            <p class="mt-1 text-sm vyd-muted">{{ $description }}</p>
        @endif
    </div>
    <x-vyd.status-badge :variant="$status" />
</div>
