@props([
    'columns' => [],
    'rows' => [],
    'statusColumn' => null,
    'maxHeight' => '24rem',
    'emptyMessage' => 'No hay filas para mostrar.',
])

@php
    $columnClass = fn (string $key): string => 'vyd-preview-table__cell--'.trim(str_replace('_', '-', $key), '-');
    $formatValue = function (string $key, mixed $value): string {
        if (! filled($value)) {
            return '';
        }

        if (str_starts_with($key, 'monto_') && is_numeric($value)) {
            return '$'.number_format((float) $value, 0, ',', '.');
        }

        return (string) $value;
    };
@endphp

<div class="vyd-preview-table-wrap" style="max-height: {{ $maxHeight }};">
    <table {{ $attributes->class('vyd-preview-table') }}>
        <thead class="vyd-preview-table__head">
            <tr>
                @foreach ($columns as $key => $label)
                    <th
                        scope="col"
                        class="vyd-preview-table__cell vyd-preview-table__cell--head {{ $columnClass($key) }}"
                    >{{ $label }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr class="vyd-preview-table__row">
                    @foreach ($columns as $key => $label)
                        <td class="vyd-preview-table__cell {{ $columnClass($key) }}">
                            @if ($statusColumn === $key)
                                <x-vyd.status-badge :variant="$row[$key] ?? 'pending'" />
                            @else
                                <span class="vyd-preview-table__value">
                                    {!! filled($row[$key] ?? null) ? e($formatValue($key, $row[$key])) : '&mdash;' !!}
                                </span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ max(count($columns), 1) }}" class="vyd-preview-table__empty">{{ $emptyMessage }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
