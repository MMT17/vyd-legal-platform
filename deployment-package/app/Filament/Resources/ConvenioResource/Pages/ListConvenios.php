<?php

namespace App\Filament\Resources\ConvenioResource\Pages;

use App\Exports\ConveniosExport;
use App\Filament\Resources\ConvenioResource;
use App\Imports\ConveniosImport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Throwable;

class ListConvenios extends ListRecords
{
    protected static string $resource = ConvenioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('importarCsv')
                ->label('Importar CSV')
                ->icon(Heroicon::ArrowUpTray)
                ->visible(fn (): bool => (auth()->user()?->hasRole('administrador') ?? false)
                    || (auth()->user()?->hasRole('cliente_chilquinta_admin') ?? false))
                ->schema([
                    FileUpload::make('archivo')
                        ->label('Archivo CSV')
                        ->disk('local')
                        ->directory('imports/convenios')
                        ->acceptedFileTypes([
                            'text/csv',
                            'text/plain',
                            'application/csv',
                            'application/vnd.ms-excel',
                        ])
                        ->maxSize(51200)
                        ->preserveFilenames()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $archivo = $data['archivo'];
                    $import = new ConveniosImport(
                        userId: auth()->id(),
                        archivoOriginal: basename($archivo),
                    );

                    try {
                        Excel::import($import, $archivo, 'local', ExcelWriter::CSV);
                    } catch (Throwable $e) {
                        Log::error('Error importando convenios CSV', [
                            'message' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);

                        $historial = $import->getHistorial();

                        $historial->update([
                            'estado' => 'error',
                            'errores' => 1,
                            'detalles' => [
                                'errores' => [
                                    [
                                        'fila' => null,
                                        'motivo' => $e->getMessage(),
                                        'trace' => $e->getTraceAsString(),
                                    ],
                                ],
                            ],
                        ]);

                        activity('importacion')
                            ->causedBy(auth()->user())
                            ->performedOn($historial)
                            ->event('imported')
                            ->withProperties([
                                'tipo_importacion' => 'convenios',
                                'estado' => 'error',
                                'error' => $e->getMessage(),
                            ])
                            ->log('Importación de convenios ejecutada');

                        Notification::make()
                            ->title('No se pudo importar el archivo')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();

                        return;
                    }

                    $historial = $import->getHistorial();

                    Notification::make()
                        ->title('Importación de convenios finalizada')
                        ->body("Creados: {$historial->creados}. Actualizados: {$historial->actualizados}. Errores: {$historial->errores}.")
                        ->success()
                        ->send();
                }),
            Action::make('exportarExcel')
                ->label('Exportar Excel')
                ->icon(Heroicon::ArrowDownTray)
                ->visible(fn (): bool => auth()->user()?->can('convenios.exportar') ?? false)
                ->action(fn () => Excel::download(
                    new ConveniosExport(),
                    'convenios_' . now()->format('Y-m-d') . '.xlsx',
                    ExcelWriter::XLSX,
                )),
            Action::make('exportarCsv')
                ->label('Exportar CSV')
                ->icon(Heroicon::ArrowDownTray)
                ->visible(fn (): bool => auth()->user()?->can('convenios.exportar') ?? false)
                ->action(fn () => Excel::download(
                    new ConveniosExport(),
                    'convenios_' . now()->format('Y-m-d') . '.csv',
                    ExcelWriter::CSV,
                )),
        ];
    }
}
