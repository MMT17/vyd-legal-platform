<?php

namespace App\Filament\Widgets;

use App\Models\Contacto;
use App\Models\Convenio;
use App\Models\Proceso;
use App\Models\Querella;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class UpcomingLegalDatesWidget extends Widget
{
    protected static ?int $sort = 3;

    protected string $view = 'filament.widgets.upcoming-legal-dates-widget';

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
            'records' => $this->getRecords(),
        ];
    }

    private function getRecords(): Collection
    {
        $records = collect();
        $now = now();

        if (auth()->user()?->can('convenios.listar') ?? false) {
            $records = $records->merge(
                Convenio::query()
                    ->whereNotNull('fecha')
                    ->where('fecha', '>=', $now->toDateString())
                    ->orderBy('fecha')
                    ->limit(5)
                    ->get()
                    ->map(fn (Convenio $convenio): array => $this->record(
                        'Convenio',
                        $convenio->numero ?: 'Sin número',
                        $convenio->fecha,
                    ))
            );
        }

        if (auth()->user()?->can('querellas.listar') ?? false) {
            $records = $records->merge(
                Querella::query()
                    ->whereNotNull('fecha')
                    ->where('fecha', '>=', $now->toDateString())
                    ->orderBy('fecha')
                    ->limit(5)
                    ->get()
                    ->map(fn (Querella $querella): array => $this->record(
                        'Querella',
                        $querella->numero ?: 'Sin número',
                        $querella->fecha,
                    ))
            );
        }

        if (auth()->user()?->can('procesos.ver') ?? false) {
            $records = $records->merge(
                Proceso::query()
                    ->whereNotNull('fecha')
                    ->where('fecha', '>=', $now)
                    ->orderBy('fecha')
                    ->limit(5)
                    ->get()
                    ->map(fn (Proceso $proceso): array => $this->record(
                        'Proceso',
                        $proceso->nombre ?: ($proceso->tipo ?: 'Sin nombre'),
                        $proceso->fecha,
                    ))
            );
        }

        if (auth()->user()?->can('contactos.ver') ?? false) {
            $records = $records->merge(
                Contacto::query()
                    ->whereNotNull('fecha')
                    ->where('fecha', '>=', $now)
                    ->orderBy('fecha')
                    ->limit(5)
                    ->get()
                    ->map(fn (Contacto $contacto): array => $this->record(
                        'Contacto',
                        $contacto->comentario ? str($contacto->comentario)->limit(42)->toString() : 'Contacto programado',
                        $contacto->fecha,
                    ))
            );
        }

        return $records
            ->sortBy('date')
            ->take(12)
            ->values();
    }

    private function record(string $type, string $title, Carbon|string|null $date): array
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        return [
            'type' => $type,
            'title' => $title,
            'date' => $date,
            'days_remaining' => now()->startOfDay()->diffInDays($date->copy()->startOfDay(), false),
        ];
    }
}
