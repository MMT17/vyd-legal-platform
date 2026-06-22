<?php

namespace App\Filament\Resources\QuerellaResource\Pages;

use App\Filament\Resources\QuerellaResource;
use App\Filament\Resources\QuerellaResource\Widgets\QuerellaExecutiveSummaryWidget;
use App\Filament\Resources\QuerellaResource\Widgets\QuerellaRecentActivityWidget;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewQuerella extends ViewRecord
{
    protected static string $resource = QuerellaResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            QuerellaExecutiveSummaryWidget::class,
            QuerellaRecentActivityWidget::class,
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
