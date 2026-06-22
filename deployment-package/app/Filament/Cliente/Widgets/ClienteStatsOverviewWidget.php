<?php

namespace App\Filament\Cliente\Widgets;

use App\Models\Convenio;
use App\Models\Documento;
use App\Models\Querella;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class ClienteStatsOverviewWidget extends StatsOverviewWidget
{
    protected ?string $heading = 'Resumen disponible';

    protected ?string $description = 'Información legal disponible para Chilquinta.';

    protected int|array|null $columns = [
        'default' => 1,
        'md' => 2,
        'xl' => 4,
    ];

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $stats = Cache::remember('portal.widgets.stats-overview', 60, function (): array {
            $lastUpdated = collect([
                Convenio::query()->max('updated_at'),
                Querella::query()->max('updated_at'),
                Documento::query()->max('updated_at'),
            ])
                ->filter()
                ->map(fn (string $date): Carbon => Carbon::parse($date))
                ->sortDesc()
                ->first();

            return [
                'convenios' => Convenio::query()->count(),
                'querellas' => Querella::query()->count(),
                'documentos' => Documento::query()->count(),
                'last_updated_value' => $lastUpdated?->format('d-m-Y H:i') ?: 'Sin actividad',
                'last_updated_description' => $lastUpdated?->diffForHumans() ?: 'No hay registros actualizados.',
            ];
        });

        return [
            Stat::make('Convenios disponibles', $stats['convenios'])
                ->description('Convenios disponibles para consulta')
                ->icon(Heroicon::OutlinedDocumentText)
                ->color('primary'),
            Stat::make('Querellas disponibles', $stats['querellas'])
                ->description('Querellas disponibles para revisión')
                ->icon(Heroicon::OutlinedScale)
                ->color('warning'),
            Stat::make('Documentos disponibles', $stats['documentos'])
                ->description('Documentos cargados en la plataforma')
                ->icon(Heroicon::OutlinedDocumentDuplicate)
                ->color('success'),
            Stat::make('Última actualización', $stats['last_updated_value'])
                ->description($stats['last_updated_description'])
                ->icon(Heroicon::OutlinedClock)
                ->color('info'),
        ];
    }
}
