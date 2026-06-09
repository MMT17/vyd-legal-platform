<?php

namespace App\Filament\Pages;

use App\Support\OperationalAlerts as OperationalAlertsData;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class OperationalAlerts extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBellAlert;

    protected static string|\UnitEnum|null $navigationGroup = 'Gestión Legal';

    protected static ?string $navigationLabel = 'Alertas Operativas';

    protected static ?string $title = 'Alertas Operativas';

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.operational-alerts';

    public static function canAccess(): bool
    {
        return OperationalAlertsData::canView();
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'alerts' => OperationalAlertsData::dashboardAlerts(),
            'sections' => OperationalAlertsData::sections(),
        ];
    }
}
