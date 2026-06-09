<?php

namespace App\Filament\Resources\PracticeAreaResource\Pages;

use App\Filament\Resources\PracticeAreaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPracticeAreas extends ListRecords
{
    protected static string $resource = PracticeAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
