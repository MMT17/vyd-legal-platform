<?php

namespace App\Filament\Widgets;

use App\Models\Querella;
use Filament\Widgets\ChartWidget;

class QuerellasPorEstadoChart extends ChartWidget
{
    protected static ?int $sort = 5;

    protected ?string $heading = 'Querellas por estado';

    protected ?string $description = 'Estado procesal resumido de querellas.';

    protected string $color = 'warning';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 1,
    ];

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        if (! (auth()->user()?->can('querellas.listar') ?? false)) {
            return [
                'datasets' => [
                    [
                        'data' => [0, 0],
                        'backgroundColor' => ['#f59e0b', '#22c55e'],
                    ],
                ],
                'labels' => ['En tramitación', 'Terminadas'],
            ];
        }

        return [
            'datasets' => [
                [
                    'data' => [
                        Querella::query()->where('estado', 'en_tramitacion')->count(),
                        Querella::query()->where('estado', 'terminada')->count(),
                    ],
                    'backgroundColor' => ['#f59e0b', '#22c55e'],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => ['En tramitación', 'Terminadas'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
