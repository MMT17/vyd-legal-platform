<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('marcar_leido')
                ->label('Marcar leído')
                ->action(fn () => $this->record->update(['read_at' => now()]))
                ->visible(fn (): bool => blank($this->record->read_at)),
        ];
    }
}
