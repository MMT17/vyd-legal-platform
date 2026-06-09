<?php

namespace App\Filament\Widgets;

use App\Models\Convenio;
use Filament\Widgets\ChartWidget;

class ConveniosPorEstadoChart extends ChartWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Convenios por estado';

    protected ?string $description = 'Distribución actual de convenios registrados.';

    protected string $color = 'primary';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 1,
    ];

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        if (! (auth()->user()?->can('convenios.listar') ?? false)) {
            return [
                'datasets' => [
                    [
                        'data' => [0, 0, 0],
                        'backgroundColor' => ['#22c55e', '#f59e0b', '#ef4444'],
                    ],
                ],
                'labels' => ['Firmados', 'En negociación', 'Frustrados'],
            ];
        }

        return [
            'datasets' => [
                [
                    'data' => [
                        Convenio::query()->where('estado', 'firmado')->count(),
                        Convenio::query()->where('estado', 'en_negociacion')->count(),
                        Convenio::query()->where('estado', 'frustrado')->count(),
                    ],
                    'backgroundColor' => ['#22c55e', '#f59e0b', '#ef4444'],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => ['Firmados', 'En negociación', 'Frustrados'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
