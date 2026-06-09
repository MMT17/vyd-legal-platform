<?php

namespace App\Filament\Resources\QuerellaResource\Pages;

use App\Exports\QuerellasExport;
use App\Filament\Resources\QuerellaResource;
use App\Imports\QuerellasImport;
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

class ListQuerellas extends ListRecords
{
    protected static string $resource = QuerellaResource::class;

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
                        ->directory('imports/querellas')
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
                    $import = new QuerellasImport(
                        userId: auth()->id(),
                        archivoOriginal: basename($archivo),
                    );

                    try {
                        Excel::import($import, $archivo, 'local', ExcelWriter::CSV);
                    } catch (Throwable $e) {
                        Log::error('Error importando querellas CSV', [
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
                                'tipo_importacion' => 'querellas',
                                'estado' => 'error',
                                'error' => $e->getMessage(),
                            ])
                            ->log('Importación de querellas ejecutada');

                        Notification::make()
                            ->title('No se pudo importar el archivo')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();

                        return;
                    }

                    $historial = $import->getHistorial();

                    Notification::make()
                        ->title('Importación de querellas finalizada')
                        ->body("Creados: {$historial->creados}. Actualizados: {$historial->actualizados}. Errores: {$historial->errores}.")
                        ->success()
                        ->send();
                }),
            Action::make('exportarExcel')
                ->label('Exportar Excel')
                ->icon(Heroicon::ArrowDownTray)
                ->visible(fn (): bool => auth()->user()?->can('querellas.exportar') ?? false)
                ->action(fn () => Excel::download(
                    new QuerellasExport(),
                    'querellas_' . now()->format('Y-m-d') . '.xlsx',
                    ExcelWriter::XLSX,
                )),
            Action::make('exportarCsv')
                ->label('Exportar CSV')
                ->icon(Heroicon::ArrowDownTray)
                ->visible(fn (): bool => auth()->user()?->can('querellas.exportar') ?? false)
                ->action(fn () => Excel::download(
                    new QuerellasExport(),
                    'querellas_' . now()->format('Y-m-d') . '.csv',
                    ExcelWriter::CSV,
                )),
        ];
    }
}
