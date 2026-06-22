<?php

namespace App\Filament\Resources\ConvenioResource\Pages;

use App\Filament\Resources\ConvenioResource;
use App\Filament\Resources\ConvenioResource\Widgets\ConvenioExecutiveSummaryWidget;
use App\Filament\Resources\ConvenioResource\Widgets\ConvenioRecentActivityWidget;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditConvenio extends EditRecord
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
            DeleteAction::make()
                ->visible(fn (): bool => auth()->user()?->hasRole('administrador') ?? false),
        ];
    }
}
