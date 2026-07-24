<?php

namespace App\Filament\Resources\QuerellaResource\Pages;

use App\Filament\Resources\Importing\ChilquintaImportPage;
use App\Filament\Resources\QuerellaResource;
use App\Services\ChilquintaCsvImporter;

class ImportQuerellas extends ChilquintaImportPage
{
    protected static string $resource = QuerellaResource::class;

    protected static string $importPermissionName = 'querellas.importar';

    protected function importType(): string
    {
        return ChilquintaCsvImporter::TYPE_QUERELLA;
    }

    protected function moduleName(): string
    {
        return 'Querellas';
    }

    protected function templatePath(): string
    {
        return 'app/import_templates/querellas_chilquinta_template.csv';
    }

    protected function templateDownloadName(): string
    {
        return 'querellas_chilquinta_template.csv';
    }

    protected function backUrl(): string
    {
        return QuerellaResource::getUrl('index');
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
            'ruc' => 'RUC',
            'rit' => 'RIT',
            'juzgado' => 'Juzgado',
            'fecha_presentacion' => 'Fecha presentacion',
        ];
    }
}
