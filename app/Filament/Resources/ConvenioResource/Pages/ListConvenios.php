<?php

namespace App\Filament\Resources\ConvenioResource\Pages;

use App\Exports\ConveniosExport;
use App\Filament\Resources\ConvenioResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Maatwebsite\Excel\Facades\Excel;

class ListConvenios extends ListRecords
{
    protected static string $resource = ConvenioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('descargarPlantillaChilquinta')
                ->label('Plantilla CSV Chilquinta')
                ->icon(Heroicon::ArrowDownTray)
                ->visible(fn (): bool => (auth()->user()?->hasRole('administrador') ?? false)
                    || (auth()->user()?->can('convenios.importar') ?? false))
                ->action(fn () => response()->download(
                    storage_path('app/import_templates/convenios_chilquinta_template.csv'),
                    'convenios_chilquinta_template.csv',
                )),
            Action::make('importarCsv')
                ->label('Importar CSV')
                ->icon(Heroicon::ArrowUpTray)
                ->visible(fn (): bool => (auth()->user()?->hasRole('administrador') ?? false)
                    || (auth()->user()?->can('convenios.importar') ?? false))
                ->url(fn (): string => ConvenioResource::getUrl('import')),
            Action::make('exportarExcel')
                ->label('Exportar Excel')
                ->icon(Heroicon::ArrowDownTray)
                ->visible(fn (): bool => auth()->user()?->can('convenios.exportar') ?? false)
                ->action(fn () => Excel::download(
                    new ConveniosExport,
                    'convenios_'.now()->format('Y-m-d').'.xlsx',
                    ExcelWriter::XLSX,
                )),
            Action::make('exportarCsv')
                ->label('Exportar CSV')
                ->icon(Heroicon::ArrowDownTray)
                ->visible(fn (): bool => auth()->user()?->can('convenios.exportar') ?? false)
                ->action(fn () => Excel::download(
                    new ConveniosExport,
                    'convenios_'.now()->format('Y-m-d').'.csv',
                    ExcelWriter::CSV,
                )),
        ];
    }
}
