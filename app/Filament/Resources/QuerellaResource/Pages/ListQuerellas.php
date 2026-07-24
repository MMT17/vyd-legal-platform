<?php

namespace App\Filament\Resources\QuerellaResource\Pages;

use App\Exports\QuerellasExport;
use App\Filament\Resources\QuerellaResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Maatwebsite\Excel\Facades\Excel;

class ListQuerellas extends ListRecords
{
    protected static string $resource = QuerellaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('descargarPlantillaChilquinta')
                ->label('Plantilla CSV Chilquinta')
                ->icon(Heroicon::ArrowDownTray)
                ->visible(fn (): bool => (auth()->user()?->hasRole('administrador') ?? false)
                    || (auth()->user()?->can('querellas.importar') ?? false))
                ->action(fn () => response()->download(
                    storage_path('app/import_templates/querellas_chilquinta_template.csv'),
                    'querellas_chilquinta_template.csv',
                )),
            Action::make('importarCsv')
                ->label('Importar CSV')
                ->icon(Heroicon::ArrowUpTray)
                ->visible(fn (): bool => (auth()->user()?->hasRole('administrador') ?? false)
                    || (auth()->user()?->can('querellas.importar') ?? false))
                ->url(fn (): string => QuerellaResource::getUrl('import')),
            Action::make('exportarExcel')
                ->label('Exportar Excel')
                ->icon(Heroicon::ArrowDownTray)
                ->visible(fn (): bool => auth()->user()?->can('querellas.exportar') ?? false)
                ->action(fn () => Excel::download(
                    new QuerellasExport,
                    'querellas_'.now()->format('Y-m-d').'.xlsx',
                    ExcelWriter::XLSX,
                )),
            Action::make('exportarCsv')
                ->label('Exportar CSV')
                ->icon(Heroicon::ArrowDownTray)
                ->visible(fn (): bool => auth()->user()?->can('querellas.exportar') ?? false)
                ->action(fn () => Excel::download(
                    new QuerellasExport,
                    'querellas_'.now()->format('Y-m-d').'.csv',
                    ExcelWriter::CSV,
                )),
        ];
    }
}
