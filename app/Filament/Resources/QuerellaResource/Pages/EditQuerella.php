<?php

namespace App\Filament\Resources\QuerellaResource\Pages;

use App\Filament\Resources\QuerellaResource;
use App\Filament\Resources\QuerellaResource\Widgets\QuerellaExecutiveSummaryWidget;
use App\Filament\Resources\QuerellaResource\Widgets\QuerellaRecentActivityWidget;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQuerella extends EditRecord
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
            DeleteAction::make()
                ->visible(fn (): bool => auth()->user()?->hasRole('administrador') ?? false),
        ];
    }
}
