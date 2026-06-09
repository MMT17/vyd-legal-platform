<?php

namespace App\Filament\Resources\QuerellaResource\Widgets;

use App\Models\Documento;
use App\Models\Proceso;
use App\Models\Querella;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity;

class QuerellaRecentActivityWidget extends Widget
{
    public ?Querella $record = null;

    protected string $view = 'filament.resources.legal-record-recent-activity-widget';

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'title' => 'Actividad reciente',
            'description' => 'Últimas 10 actividades relacionadas con esta querella.',
            'records' => $this->getRecords(),
        ];
    }

    private function getRecords(): Collection
    {
        $querella = $this->record;

        if (! $querella) {
            return collect();
        }

        $procesoIds = $querella->procesos()->pluck('id')->all();
        $documentoIds = $querella->documentos()->pluck('id')->all();

        return Activity::query()
            ->select(['id', 'description', 'event', 'causer_type', 'causer_id', 'subject_type', 'subject_id', 'created_at'])
            ->with('causer')
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
            ->limit(10)
            ->get();
    }
}
