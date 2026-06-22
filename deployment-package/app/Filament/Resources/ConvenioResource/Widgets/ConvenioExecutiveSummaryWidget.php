<?php

namespace App\Filament\Resources\ConvenioResource\Widgets;

use App\Models\Contacto;
use App\Models\Convenio;
use App\Models\Documento;
use App\Models\Proceso;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;

class ConvenioExecutiveSummaryWidget extends StatsOverviewWidget
{
    public ?Convenio $record = null;

    protected ?string $heading = 'Resumen Ejecutivo';

    protected ?string $description = 'Vista 360° del convenio, sus relaciones y su actividad reciente.';

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
        $convenio = $this->record;

        if (! $convenio) {
            return [];
        }

        $convenio->loadCount(['procesos', 'documentos', 'contactos']);
        $ultimaActividad = $this->latestActivity($convenio);

        return [
            Stat::make('Número Convenio', $convenio->numero ?: 'Sin número')
                ->description('Identificador principal')
                ->icon(Heroicon::OutlinedHashtag)
                ->color('primary'),
            Stat::make('Estado', $this->estadoLabel($convenio->estado))
                ->description('Situación actual')
                ->icon($this->estadoIcon($convenio->estado))
                ->color($this->estadoColor($convenio->estado)),
            Stat::make('Ciudad', $convenio->ciudad ?: 'Sin ciudad')
                ->description('Jurisdicción o plaza asociada')
                ->icon(Heroicon::OutlinedMapPin)
                ->color('info'),
            Stat::make('Fecha', $this->formatDate($convenio->fecha))
                ->description('Fecha principal del caso')
                ->icon(Heroicon::OutlinedCalendarDays)
                ->color('gray'),
            Stat::make('Fecha cierre', $this->formatDate($convenio->fecha_cierre))
                ->description('Cierre administrativo o legal')
                ->icon(Heroicon::OutlinedClock)
                ->color('gray'),
            Stat::make('Abogado responsable', $convenio->abogado_responsable ?: 'Sin asignar')
                ->description('Responsable de gestión')
                ->icon(Heroicon::OutlinedUserCircle)
                ->color('primary'),
            Stat::make('Procesos', (int) $convenio->procesos_count)
                ->description('Procesos vinculados')
                ->icon(Heroicon::OutlinedClipboardDocumentList)
                ->color('warning'),
            Stat::make('Documentos', (int) $convenio->documentos_count)
                ->description('Documentos cargados')
                ->icon(Heroicon::OutlinedDocumentDuplicate)
                ->color('success'),
            Stat::make('Contactos', (int) $convenio->contactos_count)
                ->description('Contactos registrados')
                ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                ->color('info'),
            Stat::make('Última actividad', $ultimaActividad?->created_at?->diffForHumans() ?: 'Sin actividad')
                ->description($ultimaActividad?->description ?: 'No hay movimientos auditados')
                ->icon(Heroicon::OutlinedClock)
                ->color('gray'),
        ];
    }

    private function latestActivity(Convenio $convenio): ?Activity
    {
        $procesoIds = $convenio->procesos()->pluck('id')->all();
        $documentoIds = $convenio->documentos()->pluck('id')->all();
        $contactoIds = $convenio->contactos()->pluck('id')->all();

        return Activity::query()
            ->select(['id', 'description', 'event', 'subject_type', 'subject_id', 'created_at'])
            ->where(function (Builder $query) use ($convenio, $procesoIds, $documentoIds, $contactoIds): void {
                $query
                    ->where(fn (Builder $query) => $query
                        ->where('subject_type', $convenio->getMorphClass())
                        ->where('subject_id', $convenio->getKey()))
                    ->orWhere(fn (Builder $query) => $query
                        ->where('subject_type', app(Proceso::class)->getMorphClass())
                        ->whereIn('subject_id', $procesoIds ?: [0]))
                    ->orWhere(fn (Builder $query) => $query
                        ->where('subject_type', app(Documento::class)->getMorphClass())
                        ->whereIn('subject_id', $documentoIds ?: [0]))
                    ->orWhere(fn (Builder $query) => $query
                        ->where('subject_type', app(Contacto::class)->getMorphClass())
                        ->whereIn('subject_id', $contactoIds ?: [0]));
            })
            ->latest('created_at')
            ->first();
    }

    private function estadoLabel(?string $estado): string
    {
        return match ($estado) {
            'firmado' => 'Firmado',
            'en_negociacion' => 'En negociación',
            'frustrado' => 'Frustrado',
            default => $estado ?: 'Sin estado',
        };
    }

    private function estadoColor(?string $estado): string
    {
        return match ($estado) {
            'firmado' => 'success',
            'en_negociacion' => 'warning',
            'frustrado' => 'danger',
            default => 'gray',
        };
    }

    private function estadoIcon(?string $estado): Heroicon
    {
        return match ($estado) {
            'firmado' => Heroicon::OutlinedCheckCircle,
            'frustrado' => Heroicon::OutlinedXCircle,
            default => Heroicon::OutlinedClock,
        };
    }

    private function formatDate(Carbon|string|null $date): string
    {
        return $date ? Carbon::parse($date)->format('d-m-Y') : 'Sin fecha';
    }
}
