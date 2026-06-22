<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\OperationalAlerts;
use App\Support\OperationalAlerts as OperationalAlertsData;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OperationalAlertsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 6;

    protected ?string $heading = 'Alertas operativas';

    protected ?string $description = 'Riesgos y pendientes detectados automáticamente.';

    protected int|array|null $columns = [
        'default' => 1,
        'md' => 2,
        'xl' => 4,
    ];

    public static function canView(): bool
    {
        return OperationalAlertsData::canView();
    }

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        return collect(OperationalAlertsData::dashboardAlerts())
            ->map(fn (array $alert): Stat => Stat::make($alert['type'], $alert['count'])
                ->description($alert['description'])
                ->icon($this->iconForColor($alert['color']))
                ->color($alert['color'])
                ->url(OperationalAlerts::getUrl()))
            ->all();
    }

    private function iconForColor(string $color): Heroicon
    {
        return match ($color) {
            'danger' => Heroicon::OutlinedExclamationTriangle,
            'warning' => Heroicon::OutlinedBellAlert,
            default => Heroicon::OutlinedFlag,
        };
    }
}
