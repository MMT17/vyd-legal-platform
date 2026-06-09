<?php

namespace App\Filament\Widgets;

use App\Models\Convenio;
use App\Models\Documento;
use App\Models\Querella;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ActividadMensualChart extends ChartWidget
{
    protected static ?int $sort = 5;

    protected ?string $heading = 'Actividad mensual';

    protected ?string $description = 'Convenios, querellas y documentos creados durante los últimos 6 meses.';

    protected string $color = 'info';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->can('reportes.ver') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        return Cache::remember('dashboard.actividad-mensual.' . md5(json_encode([
            auth()->user()?->can('convenios.listar') ?? false,
            auth()->user()?->can('querellas.listar') ?? false,
            auth()->user()?->can('documentos.ver') ?? false,
        ])), 60, function (): array {
            $months = $this->lastSixMonths();

            return [
                'datasets' => [
                    [
                        'label' => 'Convenios',
                        'data' => $this->monthlyCounts(Convenio::class, $months, 'convenios.listar'),
                        'backgroundColor' => '#d97706',
                    ],
                    [
                        'label' => 'Querellas',
                        'data' => $this->monthlyCounts(Querella::class, $months, 'querellas.listar'),
                        'backgroundColor' => '#2563eb',
                    ],
                    [
                        'label' => 'Documentos',
                        'data' => $this->monthlyCounts(Documento::class, $months, 'documentos.ver'),
                        'backgroundColor' => '#16a34a',
                    ],
                ],
                'labels' => $months
                    ->map(fn (Carbon $month): string => ucfirst($month->translatedFormat('M Y')))
                    ->all(),
            ];
        });
    }

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * @return Collection<int, Carbon>
     */
    private function lastSixMonths(): Collection
    {
        $start = now()->startOfMonth()->subMonths(5);

        return collect(range(0, 5))
            ->map(fn (int $offset): Carbon => $start->copy()->addMonths($offset));
    }

    /**
     * @param  class-string<Model>  $model
     * @param  Collection<int, Carbon>  $months
     * @return array<int, int>
     */
    private function monthlyCounts(string $model, Collection $months, string $permission): array
    {
        if (! (auth()->user()?->can($permission) ?? false)) {
            return array_fill(0, $months->count(), 0);
        }

        $counts = $model::query()
            ->whereBetween('created_at', [
                $months->first()->copy()->startOfMonth(),
                $months->last()->copy()->endOfMonth(),
            ])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month_key, count(*) as total')
            ->groupBy('month_key')
            ->pluck('total', 'month_key');

        return $months
            ->map(fn (Carbon $month): int => (int) $counts->get($month->format('Y-m'), 0))
            ->all();
    }
}
