<?php

namespace App\Providers\Filament;

use App\Filament\Cliente\Pages\DashboardCliente;
use App\Filament\Cliente\Widgets\ClienteRecentCasesWidget;
use App\Filament\Cliente\Widgets\ClienteRecentDocumentsWidget;
use App\Filament\Cliente\Widgets\ClienteStatsOverviewWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ClientePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('cliente')
            ->path('portal')
            ->login()
            ->brandName('Portal Legal Chilquinta')
            // ->brandLogo(asset('images/portal-chilquinta-logo.svg'))
            ->darkMode()
            ->colors([
                'primary' => Color::Indigo,
                'gray' => Color::Zinc,
            ])
            ->discoverResources(in: app_path('Filament/Cliente/Resources'), for: 'App\Filament\Cliente\Resources')
            ->pages([
                DashboardCliente::class,
            ])
            ->widgets([
                ClienteStatsOverviewWidget::class,
                ClienteRecentDocumentsWidget::class,
                ClienteRecentCasesWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
