<?php

namespace App\Filament\Resources\ConvenioResource\Pages;

use App\Filament\Resources\ConvenioResource;
use App\Filament\Resources\Importing\ChilquintaImportPage;
use App\Services\ChilquintaCsvImporter;

class ImportConvenios extends ChilquintaImportPage
{
    protected static string $resource = ConvenioResource::class;

    protected static string $importPermissionName = 'convenios.importar';

    protected function importType(): string
    {
        return ChilquintaCsvImporter::TYPE_CONVENIO;
    }

    protected function moduleName(): string
    {
        return 'Convenios';
    }

    protected function templatePath(): string
    {
        return 'app/import_templates/convenios_chilquinta_template.csv';
    }

    protected function templateDownloadName(): string
    {
        return 'convenios_chilquinta_template.csv';
    }

    protected function backUrl(): string
    {
        return ConvenioResource::getUrl('index');
    }

    /**
     * @return array<string, string>
     */
    protected function visibleColumns(): array
    {
        return [
            '__row' => 'Fila',
            '__status' => 'Resultado esperado',
            'caso' => 'Caso',
            'nis' => 'NIS',
            'nombre' => 'Nombre',
            'comuna' => 'Comuna',
            'monto_total' => 'Monto total',
        ];
    }
}
