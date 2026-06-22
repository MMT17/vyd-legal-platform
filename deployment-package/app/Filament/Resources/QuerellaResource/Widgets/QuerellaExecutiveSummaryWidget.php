<?php

namespace App\Filament\Resources\QuerellaResource\Widgets;

use App\Models\Documento;
use App\Models\Proceso;
use App\Models\Querella;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;

class QuerellaExecutiveSummaryWidget extends StatsOverviewWidget
{
    public ?Querella $record = null;

    protected ?string $heading = 'Resumen Ejecutivo';

    protected ?string $description = 'Vista 360° de la querella, sus relaciones y su actividad reciente.';

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
        $querella = $this->record;

        if (! $querella) {
            return [];
        }

        $querella->loadCount(['procesos', 'documentos']);
        $ultimaActividad = $this->latestActivity($querella);

        return [
            Stat::make('Número Querella', $querella->numero ?: 'Sin número')
                ->description('Identificador principal')
                ->icon(Heroicon::OutlinedHashtag)
                ->color('primary'),
            Stat::make('Estado', $this->estadoLabel($querella->estado))
                ->description('Situación actual')
                ->icon($this->estadoIcon($querella->estado))
                ->color($this->estadoColor($querella->estado)),
            Stat::make('Tribunal', $querella->tribunal ?: 'Sin tribunal')
                ->description('Tribunal asociado')
                ->icon(Heroicon::OutlinedBuildingLibrary)
                ->color('info'),
            Stat::make('Tipo proceso', $querella->tipo_proceso ?: 'Sin tipo')
                ->description('Clasificación procesal')
                ->icon(Heroicon::OutlinedBriefcase)
                ->color('warning'),
            Stat::make('Fecha', $this->formatDate($querella->fecha))
                ->description('Fecha principal del caso')
                ->icon(Heroicon::OutlinedCalendarDays)
                ->color('gray'),
            Stat::make('Fecha cierre', $this->formatDate($querella->fecha_cierre))
                ->description('Cierre administrativo o legal')
                ->icon(Heroicon::OutlinedClock)
                ->color('gray'),
            Stat::make('Abogado responsable', $querella->abogado_responsable ?: 'Sin asignar')
                ->description('Responsable de gestión')
                ->icon(Heroicon::OutlinedUserCircle)
                ->color('primary'),
            Stat::make('Procesos', (int) $querella->procesos_count)
                ->description('Procesos vinculados')
                ->icon(Heroicon::OutlinedClipboardDocumentList)
                ->color('warning'),
            Stat::make('Documentos', (int) $querella->documentos_count)
                ->description('Documentos cargados')
                ->icon(Heroicon::OutlinedDocumentDuplicate)
                ->color('success'),
            Stat::make('Última actividad', $ultimaActividad?->created_at?->diffForHumans() ?: 'Sin actividad')
                ->description($ultimaActividad?->description ?: 'No hay movimientos auditados')
                ->icon(Heroicon::OutlinedClock)
                ->color('gray'),
        ];
    }

    private function latestActivity(Querella $querella): ?Activity
    {
        $procesoIds = $querella->procesos()->pluck('id')->all();
        $documentoIds = $querella->documentos()->pluck('id')->all();

        return Activity::query()
            ->select(['id', 'description', 'event', 'subject_type', 'subject_id', 'created_at'])
            ->where(function (Builder $query) use ($querella, $procesoIds, $documentoIds): void {
                $query
                    ->where(fn (Builder $query) => $query
                        ->where('subject_type', $querella->getMorphClass())
                        ->where('subject_id', $querella->getKey()))
                    ->orWhere(fn (Builder $query) => $query
                        ->where('subject_type', app(Proceso::class)->getMorphClass())
                        ->whereIn('subject_id', $procesoIds ?: [0]))
                    ->orWhere(fn (Builder $query) => $query
                        ->where('subject_type', app(Documento::class)->getMorphClass())
                        ->whereIn('subject_id', $documentoIds ?: [0]));
            })
            ->latest('created_at')
            ->first();
    }

    private function estadoLabel(?string $estado): string
    {
        return match ($estado) {
            'en_tramitacion' => 'En tramitación',
            'terminada' => 'Terminada',
            default => $estado ?: 'Sin estado',
        };
    }

    private function estadoColor(?string $estado): string
    {
        return match ($estado) {
            'en_tramitacion' => 'warning',
            'terminada' => 'success',
            default => 'gray',
        };
    }

    private function estadoIcon(?string $estado): Heroicon
    {
        return match ($estado) {
            'terminada' => Heroicon::OutlinedCheckCircle,
            default => Heroicon::OutlinedClock,
        };
    }

    private function formatDate(Carbon|string|null $date): string
    {
        return $date ? Carbon::parse($date)->format('d-m-Y') : 'Sin fecha';
    }
}
