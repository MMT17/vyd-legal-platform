<?php

namespace App\Filament\Widgets;

use App\Models\Convenio;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class ConveniosEstadoChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Convenios por estado';

    protected ?string $description = 'Distribución actual de convenios registrados.';

    protected string $color = 'primary';

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
        $canListConvenios = auth()->user()?->can('convenios.listar') ?? false;
        $counts = collect(Cache::remember('dashboard.chart.convenios-estado.' . (int) $canListConvenios, 60, fn (): array => $canListConvenios
            ? Convenio::query()->selectRaw('estado, count(*) as total')->groupBy('estado')->pluck('total', 'estado')->all()
            : []));

        return [
            'datasets' => [
                [
                    'data' => [
                        (int) $counts->get('firmado', 0),
                        (int) $counts->get('en_negociacion', 0),
                        (int) $counts->get('frustrado', 0),
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
