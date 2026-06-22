<?php

namespace App\Filament\Widgets;

use App\Models\Convenio;
use App\Models\Documento;
use App\Models\Querella;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LegalStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Resumen legal';

    protected ?string $description = 'Indicadores principales de convenios, querellas y documentos.';

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
        $canListConvenios = auth()->user()?->can('convenios.listar') ?? false;
        $canListQuerellas = auth()->user()?->can('querellas.listar') ?? false;
        $canViewDocumentos = auth()->user()?->can('documentos.ver') ?? false;

        return [
            Stat::make('Total Convenios', $canListConvenios ? Convenio::query()->count() : 0)
                ->description('Casos de convenio registrados')
                ->icon(Heroicon::OutlinedDocumentText)
                ->color('primary'),
            Stat::make('Convenios Firmados', $canListConvenios ? Convenio::query()->where('estado', 'firmado')->count() : 0)
                ->description('Convenios cerrados favorablemente')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success'),
            Stat::make('Convenios en Negociación', $canListConvenios ? Convenio::query()->where('estado', 'en_negociacion')->count() : 0)
                ->description('Gestiones activas')
                ->icon(Heroicon::OutlinedClock)
                ->color('warning'),
            Stat::make('Convenios Frustrados', $canListConvenios ? Convenio::query()->where('estado', 'frustrado')->count() : 0)
                ->description('Negociaciones sin cierre')
                ->icon(Heroicon::OutlinedXCircle)
                ->color('danger'),
            Stat::make('Total Querellas', $canListQuerellas ? Querella::query()->count() : 0)
                ->description('Querellas registradas')
                ->icon(Heroicon::OutlinedScale)
                ->color('primary'),
            Stat::make('Querellas en Tramitación', $canListQuerellas ? Querella::query()->where('estado', 'en_tramitacion')->count() : 0)
                ->description('Procedimientos activos')
                ->icon(Heroicon::OutlinedClipboardDocumentList)
                ->color('warning'),
            Stat::make('Querellas Terminadas', $canListQuerellas ? Querella::query()->where('estado', 'terminada')->count() : 0)
                ->description('Procedimientos finalizados')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success'),
            Stat::make('Documentos Registrados', $canViewDocumentos ? Documento::query()->count() : 0)
                ->description('Archivos y respaldos cargados')
                ->icon(Heroicon::OutlinedDocumentDuplicate)
                ->color('info'),
        ];
    }
}
