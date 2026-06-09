<?php

namespace App\Filament\Widgets;

use App\Models\Convenio;
use App\Models\Documento;
use App\Models\Querella;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class RecentLegalActivityWidget extends Widget
{
    protected static ?int $sort = 6;

    protected string $view = 'filament.widgets.recent-legal-activity-widget';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 1,
    ];

    public static function canView(): bool
    {
        return auth()->user()?->can('auditoria.ver') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'records' => $this->getRecords(),
        ];
    }

    private function getRecords(): Collection
    {
        if (! (auth()->user()?->can('auditoria.ver') ?? false)) {
            return collect();
        }

        return Cache::remember('dashboard.recent-legal-activity.' . auth()->id(), 60, fn (): Collection => $this->queryRecords());
    }

    private function queryRecords(): Collection
    {
        $records = collect();

        if (auth()->user()?->can('convenios.listar') ?? false) {
            $records = $records->merge(
                Convenio::query()
                    ->select(['id', 'numero', 'estado', 'created_at'])
                    ->latest()
                    ->limit(10)
                    ->get()
                    ->map(fn (Convenio $convenio): array => [
                        'type' => 'Convenio',
                        'title' => $convenio->numero ?: 'Sin número',
                        'state' => $convenio->estado,
                        'created_at' => $convenio->created_at,
                    ])
            );
        }

        if (auth()->user()?->can('querellas.listar') ?? false) {
            $records = $records->merge(
                Querella::query()
                    ->select(['id', 'numero', 'estado', 'created_at'])
                    ->latest()
                    ->limit(10)
                    ->get()
                    ->map(fn (Querella $querella): array => [
                        'type' => 'Querella',
                        'title' => $querella->numero ?: 'Sin número',
                        'state' => $querella->estado,
                        'created_at' => $querella->created_at,
                    ])
            );
        }

        if (auth()->user()?->can('documentos.ver') ?? false) {
            $records = $records->merge(
                Documento::query()
                    ->select(['id', 'nombre', 'tipo_documento', 'created_at'])
                    ->latest()
                    ->limit(10)
                    ->get()
                    ->map(fn (Documento $documento): array => [
                        'type' => 'Documento',
                        'title' => $documento->nombre ?: 'Sin nombre',
                        'state' => $documento->tipo_documento,
                        'created_at' => $documento->created_at,
                    ])
            );
        }

        return $records
            ->sortByDesc('created_at')
            ->take(10)
            ->values();
    }
}
