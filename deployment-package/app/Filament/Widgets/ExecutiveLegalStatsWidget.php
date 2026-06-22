<?php

namespace App\Filament\Widgets;

use App\Models\Convenio;
use App\Models\Documento;
use App\Models\Querella;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class ExecutiveLegalStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Dashboard ejecutivo legal';

    protected ?string $description = 'Indicadores operativos principales de Plataforma Legal VyD.';

    protected int|array|null $columns = [
        'default' => 1,
        'md' => 2,
        'xl' => 4,
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
        $canViewDocumentos = auth()->user()?->can('documentos.ver') ?? false;

        $data = Cache::remember('dashboard.executive-stats.' . md5(json_encode([
            $canListConvenios,
            $canListQuerellas,
            $canViewDocumentos,
        ])), 60, fn (): array => [
            'convenios' => $canListConvenios
                ? Convenio::query()->selectRaw('estado, count(*) as total')->groupBy('estado')->pluck('total', 'estado')->all()
                : [],
            'querellas' => $canListQuerellas
                ? Querella::query()->selectRaw('estado, count(*) as total')->groupBy('estado')->pluck('total', 'estado')->all()
                : [],
            'documentos' => $canViewDocumentos ? Documento::query()->count() : 0,
        ]);

        $conveniosPorEstado = collect($data['convenios']);
        $querellasPorEstado = collect($data['querellas']);

        return [
            Stat::make('Total Convenios', $conveniosPorEstado->sum())
                ->description('Convenios registrados')
                ->icon(Heroicon::OutlinedDocumentText)
                ->color('primary'),
            Stat::make('Convenios Firmados', (int) $conveniosPorEstado->get('firmado', 0))
                ->description('Convenios cerrados')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success'),
            Stat::make('Convenios en Negociación', (int) $conveniosPorEstado->get('en_negociacion', 0))
                ->description('Gestiones activas')
                ->icon(Heroicon::OutlinedClock)
                ->color('warning'),
            Stat::make('Convenios Frustrados', (int) $conveniosPorEstado->get('frustrado', 0))
                ->description('Negociaciones sin cierre')
                ->icon(Heroicon::OutlinedXCircle)
                ->color('danger'),
            Stat::make('Total Querellas', $querellasPorEstado->sum())
                ->description('Querellas registradas')
                ->icon(Heroicon::OutlinedScale)
                ->color('primary'),
            Stat::make('Querellas en Tramitación', (int) $querellasPorEstado->get('en_tramitacion', 0))
                ->description('Procedimientos activos')
                ->icon(Heroicon::OutlinedClipboardDocumentList)
                ->color('warning'),
            Stat::make('Querellas Terminadas', (int) $querellasPorEstado->get('terminada', 0))
                ->description('Procedimientos finalizados')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success'),
            Stat::make('Documentos Registrados', $data['documentos'])
                ->description('Respaldos cargados')
                ->icon(Heroicon::OutlinedDocumentDuplicate)
                ->color('info'),
        ];
    }
}
