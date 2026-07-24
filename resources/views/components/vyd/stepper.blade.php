@props([
    'steps' => [],
    'current' => 1,
    'completed' => [],
])

<ol {{ $attributes->class('vyd-stepper') }} aria-label="Progreso de la importación">
    @foreach ($steps as $index => $step)
        @php
            $number = is_int($index) ? $index : $loop->iteration;
            $status = in_array($number, $completed, true) ? 'completed' : ((int) $current === (int) $number ? 'active' : 'pending');
        @endphp
        <li class="vyd-stepper__item vyd-stepper__item--{{ $status }}" aria-current="{{ $status === 'active' ? 'step' : 'false' }}">
            <span class="vyd-stepper__marker" aria-hidden="true">
                @if ($status === 'completed')
                    <x-filament::icon icon="heroicon-m-check" class="h-4 w-4" />
                @else
                    {{ $number }}
                @endif
            </span>
            <span class="vyd-stepper__label">
                {{ $step }}
            </span>
            @if (! $loop->last)
                <span class="vyd-stepper__connector" aria-hidden="true"></span>
            @endif
        </li>
    @endforeach
</ol>
