<?php

namespace App\Filament\Widgets;

use App\Models\Querella;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class QuerellasEstadoChart extends ChartWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Querellas por estado';

    protected ?string $description = 'Estado procesal resumido de querellas.';

    protected string $color = 'warning';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 1,
    ];

    public static function canView(): bool
    {
        return auth()->user()?->can('reportes.ver') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $canListQuerellas = auth()->user()?->can('querellas.listar') ?? false;
        $counts = collect(Cache::remember('dashboard.chart.querellas-estado.' . (int) $canListQuerellas, 60, fn (): array => $canListQuerellas
            ? Querella::query()->selectRaw('estado, count(*) as total')->groupBy('estado')->pluck('total', 'estado')->all()
            : []));

        return [
            'datasets' => [
                [
                    'data' => [
                        (int) $counts->get('en_tramitacion', 0),
                        (int) $counts->get('terminada', 0),
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
