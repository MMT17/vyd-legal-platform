<?php

namespace App\Filament\Resources\ConvenioResource\Widgets;

use App\Models\Contacto;
use App\Models\Convenio;
use App\Models\Documento;
use App\Models\Proceso;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity;

class ConvenioRecentActivityWidget extends Widget
{
    public ?Convenio $record = null;

    protected string $view = 'filament.resources.legal-record-recent-activity-widget';

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'title' => 'Actividad reciente',
            'description' => 'Últimas 10 actividades relacionadas con este convenio.',
            'records' => $this->getRecords(),
        ];
    }

    private function getRecords(): Collection
    {
        $convenio = $this->record;

        if (! $convenio) {
            return collect();
        }

        $procesoIds = $convenio->procesos()->pluck('id')->all();
        $documentoIds = $convenio->documentos()->pluck('id')->all();
        $contactoIds = $convenio->contactos()->pluck('id')->all();

        return Activity::query()
            ->select(['id', 'description', 'event', 'causer_type', 'causer_id', 'subject_type', 'subject_id', 'created_at'])
            ->with('causer')
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
            ->limit(10)
            ->get();
    }
}
