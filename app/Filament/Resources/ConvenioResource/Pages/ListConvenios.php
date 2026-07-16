<?php

namespace App\Filament\Resources\ConvenioResource\Pages;

use App\Exports\ConveniosExport;
use App\Filament\Resources\ConvenioResource;
use App\Services\ChilquintaCsvImporter;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

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
                    || (auth()->user()?->hasRole('cliente_chilquinta_admin') ?? false))
                ->action(fn () => response()->download(
                    storage_path('app/import_templates/convenios_chilquinta_template.csv'),
                    'convenios_chilquinta_template.csv',
                )),
            Action::make('importarCsv')
                ->label('Importar CSV')
                ->icon(Heroicon::ArrowUpTray)
                ->visible(fn (): bool => (auth()->user()?->hasRole('administrador') ?? false)
                    || (auth()->user()?->can('convenios.importar') ?? false))
                ->requiresConfirmation()
                ->modalHeading('Confirmar importacion de convenios')
                ->modalDescription('El archivo se validara fila por fila. Las filas con error se omitiran y se registrara un reporte descargable en el historial.')
                ->schema([
                    Placeholder::make('formato_chilquinta')
                        ->label('Formato CSV Chilquinta')
                        ->content(new HtmlString(
                            '<div class="space-y-2 text-sm">'.
                            '<p><strong>Separador:</strong> coma. <strong>Encoding recomendado:</strong> UTF-8.</p>'.
                            '<p><strong>Columnas esperadas:</strong> caso, nis, energia_ventana, energia_fv, energia_total, monto_ventana, monto_fv, monto_total, meses_ventana, meses_fv, meses_total, tipo_cnr, tipo_irregularidad, nombre, direccion, comuna, telefono.</p>'.
                            '<p>Se aceptan encabezados con mayusculas, acentos y variantes conocidas como energia_fv, tipo_CNR, direccion, telefono, telefonos o telefonos con acento.</p>'.
                            '</div>'
                        )),
                    FileUpload::make('archivo')
                        ->label('Archivo CSV')
                        ->disk('local')
                        ->acceptedFileTypes([
                            'text/csv',
                            'text/plain',
                            'application/csv',
                            'application/vnd.ms-excel',
                        ])
                        ->maxSize(20480)
                        ->storeFiles(false)
                        ->reactive()
                        ->required(),
                    Placeholder::make('prevalidacion')
                        ->label('Validacion previa')
                        ->content(fn (Get $get): HtmlString => $this->previewImport(
                            $get('archivo'),
                            ChilquintaCsvImporter::TYPE_CONVENIO,
                        )),
                ])
                ->action(function (array $data): void {
                    try {
                        $staged = $this->stageUploadedCsv($data['archivo'], ChilquintaCsvImporter::TYPE_CONVENIO);
                        $analysis = app(ChilquintaCsvImporter::class)->analyze($staged['absolute_path'], ChilquintaCsvImporter::TYPE_CONVENIO);

                        if (array_intersect(['caso', 'nis'], $analysis['missing_columns']) !== []) {
                            Notification::make()
                                ->title('No se puede importar')
                                ->body('Faltan columnas requeridas: '.implode(', ', array_intersect(['caso', 'nis'], $analysis['missing_columns'])))
                                ->danger()
                                ->send();

                            return;
                        }

                        $result = app(ChilquintaCsvImporter::class)->import(
                            path: $staged['absolute_path'],
                            type: ChilquintaCsvImporter::TYPE_CONVENIO,
                            userId: auth()->id(),
                            originalFilename: $staged['original_name'],
                            storedFilename: $staged['relative_path'],
                        );
                    } catch (Throwable $e) {
                        Log::error('Error importando convenios CSV', [
                            'message' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);

                        Notification::make()
                            ->title('No se pudo importar el archivo')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('Importacion de convenios finalizada')
                        ->body("Creados: {$result['created']}. Actualizados: {$result['updated']}. Omitidos: {$result['skipped']}. Errores: {$result['errors']}.")
                        ->status($result['errors'] > 0 ? 'warning' : 'success')
                        ->send();
                }),
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

    private function previewImport(mixed $archivo, string $type): HtmlString
    {
        $upload = $this->resolveUploadedCsv($archivo);

        if ($upload === null) {
            return new HtmlString('<p class="text-sm text-gray-500">Sube un archivo CSV para ver el resumen antes de confirmar.</p>');
        }

        try {
            $analysis = app(ChilquintaCsvImporter::class)->analyze($upload['absolute_path'], $type);
        } catch (Throwable $e) {
            return new HtmlString('<p class="text-sm text-danger-600">No se pudo analizar el CSV: '.e($e->getMessage()).'</p>');
        }

        $columns = e(implode(', ', $analysis['columns']));
        $missing = $analysis['missing_columns'] === [] ? 'Ninguna' : e(implode(', ', $analysis['missing_columns']));
        $unknown = $analysis['unknown_columns'] === [] ? 'Ninguna' : e(implode(', ', $analysis['unknown_columns']));
        $preview = collect($analysis['preview'])
            ->take(5)
            ->map(fn (array $row): string => e(json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)))
            ->implode('<br>');

        return new HtmlString(
            '<div class="space-y-2 text-sm">'.
            '<p><strong>Archivo:</strong> '.e($upload['original_name']).' <strong>Tamano:</strong> '.e((string) $upload['size']).' bytes</p>'.
            '<p><strong>Separador:</strong> '.e($analysis['delimiter']).' <strong>Encoding:</strong> '.e($analysis['encoding']).'</p>'.
            '<p><strong>Filas:</strong> '.e((string) $analysis['total_rows']).' <strong>Validas:</strong> '.e((string) $analysis['valid_rows']).' <strong>Invalidas:</strong> '.e((string) $analysis['invalid_rows']).'</p>'.
            '<p><strong>Nuevos estimados:</strong> '.e((string) $analysis['new_rows']).' <strong>Actualizaciones estimadas:</strong> '.e((string) $analysis['existing_rows']).'</p>'.
            '<p><strong>Columnas:</strong> '.$columns.'</p>'.
            '<p><strong>Faltantes:</strong> '.$missing.'</p>'.
            '<p><strong>Desconocidas:</strong> '.$unknown.'</p>'.
            '<p><strong>Primeras filas:</strong><br>'.$preview.'</p>'.
            '</div>'
        );
    }

    /**
     * @return array{absolute_path:string,original_name:string,size:int}|null
     */
    private function resolveUploadedCsv(mixed $archivo): ?array
    {
        if (is_array($archivo)) {
            $archivo = reset($archivo) ?: null;
        }

        if ($archivo instanceof TemporaryUploadedFile) {
            return [
                'absolute_path' => $archivo->getRealPath(),
                'original_name' => $archivo->getClientOriginalName(),
                'size' => $archivo->getSize(),
            ];
        }

        if (is_string($archivo) && Storage::disk('local')->exists($archivo)) {
            return [
                'absolute_path' => Storage::disk('local')->path($archivo),
                'original_name' => basename($archivo),
                'size' => Storage::disk('local')->size($archivo),
            ];
        }

        return null;
    }

    /**
     * @return array{absolute_path:string,relative_path:string,original_name:string,size:int}
     */
    private function stageUploadedCsv(mixed $archivo, string $type): array
    {
        $upload = $this->resolveUploadedCsv($archivo);

        if ($upload === null) {
            throw new \InvalidArgumentException('No se encontro el archivo cargado para esta importacion.');
        }

        $safeName = Str::of(pathinfo($upload['original_name'], PATHINFO_FILENAME))
            ->ascii()
            ->slug('_')
            ->limit(80, '')
            ->append('.csv')
            ->toString();
        $relativePath = "imports/{$type}/".(string) Str::uuid()."/{$safeName}";

        $stream = fopen($upload['absolute_path'], 'rb');

        if ($stream === false) {
            throw new \InvalidArgumentException('No se pudo abrir el archivo cargado.');
        }

        Storage::disk('local')->put($relativePath, $stream);
        fclose($stream);

        if (Storage::disk('local')->size($relativePath) !== $upload['size']) {
            throw new \RuntimeException('El archivo almacenado no coincide en tamano con el archivo cargado.');
        }

        return [
            'absolute_path' => Storage::disk('local')->path($relativePath),
            'relative_path' => $relativePath,
            'original_name' => $upload['original_name'],
            'size' => $upload['size'],
        ];
    }
}
