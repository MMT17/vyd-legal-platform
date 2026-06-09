<?php

namespace App\Filament\Resources\ConvenioResource\Pages;

use App\Filament\Resources\ConvenioResource;
use App\Filament\Resources\ConvenioResource\Widgets\ConvenioExecutiveSummaryWidget;
use App\Filament\Resources\ConvenioResource\Widgets\ConvenioRecentActivityWidget;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewConvenio extends ViewRecord
{
    protected static string $resource = ConvenioResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            ConvenioExecutiveSummaryWidget::class,
            ConvenioRecentActivityWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
