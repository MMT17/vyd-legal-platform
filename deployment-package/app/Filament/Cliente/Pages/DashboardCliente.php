<?php

namespace App\Filament\Cliente\Pages;

use App\Filament\Cliente\Widgets\ClienteRecentCasesWidget;
use App\Filament\Cliente\Widgets\ClienteRecentDocumentsWidget;
use App\Filament\Cliente\Widgets\ClienteStatsOverviewWidget;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Support\Icons\Heroicon;

class DashboardCliente extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Dashboard';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.cliente.pages.dashboard-cliente';

    public static function getRoutePath(Panel $panel): string
    {
        return '/';
    }

    public static function getRelativeRouteName(Panel $panel): string
    {
        return 'dashboard';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ClienteStatsOverviewWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    protected function getFooterWidgets(): array
    {
        return [
            ClienteRecentDocumentsWidget::class,
            ClienteRecentCasesWidget::class,
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return [
            'default' => 1,
            'xl' => 2,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'name' => auth()->user()?->name ?: 'Usuario',
        ];
    }
}
