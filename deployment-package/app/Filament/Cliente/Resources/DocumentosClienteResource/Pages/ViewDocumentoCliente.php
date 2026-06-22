<?php

namespace App\Filament\Cliente\Resources\DocumentosClienteResource\Pages;

use App\Filament\Cliente\Resources\DocumentosClienteResource;
use Filament\Resources\Pages\ViewRecord;

class ViewDocumentoCliente extends ViewRecord
{
    protected static string $resource = DocumentosClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DocumentosClienteResource::downloadAction(),
        ];
    }
}
