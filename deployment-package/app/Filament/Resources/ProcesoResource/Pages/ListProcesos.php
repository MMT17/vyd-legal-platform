<?php

namespace App\Filament\Resources\ProcesoResource\Pages;

use App\Filament\Resources\ProcesoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProcesos extends ListRecords
{
    protected static string $resource = ProcesoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
