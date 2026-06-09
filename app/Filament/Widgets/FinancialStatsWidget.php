<?php

namespace App\Filament\Widgets;

use App\Models\Convenio;
use App\Models\Querella;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class FinancialStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Resumen financiero';

    protected ?string $description = 'Montos consolidados de convenios y querellas.';

    protected int|array|null $columns = [
        'default' => 1,
        'md' => 2,
        'xl' => 5,
    ];

    public static function canView(): bool
    {
        return auth()->user()?->can('reportes.ver') ?? false;
    }

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $canListConvenios = auth()->user()?->can('convenios.listar') ?? false;
        $canListQuerellas = auth()->user()?->can('querellas.listar') ?? false;

        $data = Cache::remember('dashboard.financial-stats.' . md5(json_encode([
            $canListConvenios,
            $canListQuerellas,
        ])), 60, fn (): array => [
            'convenios' => $this->totals(Convenio::class, $canListConvenios),
            'querellas' => $this->totals(Querella::class, $canListQuerellas),
        ]);

        $convenios = $data['convenios'];
        $querellas = $data['querellas'];

        return [
            Stat::make('Deuda Total Gestionada', $this->money($this->sum('deuda_total', $convenios, $querellas)))
                ->description('Deuda total registrada')
                ->icon(Heroicon::OutlinedBanknotes)
                ->color('danger'),
            Stat::make('CNR 12 meses', $this->money($this->sum('cnr_12_meses', $convenios, $querellas)))
                ->description('Recuperación dentro de ventana')
                ->icon(Heroicon::OutlinedCalendarDays)
                ->color('success'),
            Stat::make('CNR fuera ventana', $this->money($this->sum('cnr_fuera_ventana', $convenios, $querellas)))
                ->description('Recuperación fuera de ventana')
                ->icon(Heroicon::OutlinedArrowTrendingUp)
                ->color('warning'),
            Stat::make('Acuerdo Extrajudicial', $this->money($this->sum('acuerdo_extrajudicial', $convenios, $querellas)))
                ->description('Acuerdos valorizados')
                ->icon(Heroicon::OutlinedDocumentCheck)
                ->color('info'),
            Stat::make('CNR pagado anterior', $this->money($this->sum('cnr_pagado_anterior', $convenios, $querellas)))
                ->description('Pagos previos registrados')
                ->icon(Heroicon::OutlinedReceiptRefund)
                ->color('gray'),
        ];
    }

    /**
     * @param  class-string<Convenio|Querella>  $model
     * @return array<string, float>
     */
    private function totals(string $model, bool $allowed): array
    {
        if (! $allowed) {
            return [];
        }

        $row = $model::query()
            ->selectRaw('
                coalesce(sum(deuda_total), 0) as deuda_total,
                coalesce(sum(cnr_12_meses), 0) as cnr_12_meses,
                coalesce(sum(cnr_fuera_ventana), 0) as cnr_fuera_ventana,
                coalesce(sum(acuerdo_extrajudicial), 0) as acuerdo_extrajudicial,
                coalesce(sum(cnr_pagado_anterior), 0) as cnr_pagado_anterior
            ')
            ->first();

        return [
            'deuda_total' => (float) $row?->deuda_total,
            'cnr_12_meses' => (float) $row?->cnr_12_meses,
            'cnr_fuera_ventana' => (float) $row?->cnr_fuera_ventana,
            'acuerdo_extrajudicial' => (float) $row?->acuerdo_extrajudicial,
            'cnr_pagado_anterior' => (float) $row?->cnr_pagado_anterior,
        ];
    }

    /**
     * @param  array<string, float>  $convenios
     * @param  array<string, float>  $querellas
     */
    private function sum(string $column, array $convenios, array $querellas): float
    {
        return ($convenios[$column] ?? 0.0) + ($querellas[$column] ?? 0.0);
    }

    private function money(float $value): string
    {
        return '$' . number_format($value, 0, ',', '.');
    }
}
