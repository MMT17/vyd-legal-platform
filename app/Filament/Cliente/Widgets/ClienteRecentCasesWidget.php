<?php

namespace App\Filament\Cliente\Widgets;

use App\Models\Convenio;
use App\Models\Querella;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ClienteRecentCasesWidget extends Widget
{
    protected string $view = 'filament.cliente.widgets.cliente-recent-cases-widget';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 1,
    ];

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'cases' => Cache::remember('portal.widgets.recent-cases', 60, fn (): Collection => $this->getCases()),
        ];
    }

    private function getCases(): Collection
    {
        $convenios = Convenio::query()
            ->select(['id', 'numero', 'estado', 'fecha', 'created_at'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (Convenio $convenio): array => [
                'tipo' => 'Convenio',
                'numero' => $convenio->numero ?: 'Sin número',
                'estado' => $this->estadoConvenio($convenio->estado),
                'fecha' => $convenio->fecha,
                'created_at' => $convenio->created_at,
            ]);

        $querellas = Querella::query()
            ->select(['id', 'numero', 'estado', 'fecha', 'created_at'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (Querella $querella): array => [
                'tipo' => 'Querella',
                'numero' => $querella->numero ?: 'Sin número',
                'estado' => $this->estadoQuerella($querella->estado),
                'fecha' => $querella->fecha,
                'created_at' => $querella->created_at,
            ]);

        return $convenios
            ->merge($querellas)
            ->sortByDesc('created_at')
            ->take(5)
            ->values();
    }

    private function estadoConvenio(?string $estado): string
    {
        return match ($estado) {
            'firmado' => 'Firmado',
            'en_negociacion' => 'En negociación',
            'frustrado' => 'Frustrado',
            default => $estado ?: 'Sin estado',
        };
    }

    private function estadoQuerella(?string $estado): string
    {
        return match ($estado) {
            'en_tramitacion' => 'En tramitación',
            'terminada' => 'Terminada',
            default => $estado ?: 'Sin estado',
        };
    }
}
