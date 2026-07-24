<?php

namespace App\Filament\Resources\Importing;

use App\Filament\Resources\ImportacionHistorialResource;
use App\Models\ImportacionHistorial;
use App\Services\ChilquintaCsvImporter;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Throwable;

abstract class ChilquintaImportPage extends Page
{
    use WithFileUploads;

    protected string $view = 'filament.imports.chilquinta-flow';

    protected static string $importPermissionName = '';

    public mixed $archivo = null;

    public string $stage = 'initial';

    /** @var array<string, mixed>|null */
    public ?array $upload = null;

    /** @var array<string, mixed>|null */
    public ?array $analysis = null;

    /** @var array<string, mixed>|null */
    public ?array $result = null;

    public ?int $historialId = null;

    public ?string $analysisError = null;

    public bool $showOnlyIssues = false;

    public bool $importing = false;

    abstract protected function importType(): string;

    abstract protected function moduleName(): string;

    abstract protected function templatePath(): string;

    abstract protected function templateDownloadName(): string;

    abstract protected function backUrl(): string;

    /**
     * @return array<string, string>
     */
    abstract protected function visibleColumns(): array;

    public static function canAccess(array $parameters = []): bool
    {
        $user = auth()->user();

        return (bool) ($user?->hasRole('administrador') || $user?->can(static::$importPermissionName));
    }

    public function mount(): void
    {
        abort_unless($this->canImport(), 403);
    }

    public function updatedArchivo(): void
    {
        $this->resetReviewState();

        $upload = $this->resolveUploadedCsv();

        if ($upload === null) {
            $this->stage = 'initial';

            return;
        }

        if (! $this->hasCsvExtension($upload['original_name'])) {
            $this->upload = $upload;
            $this->analysisError = 'El archivo seleccionado debe tener extension .csv.';
            $this->stage = 'validation_error';

            return;
        }

        $this->upload = $upload;
        $this->stage = 'selected';
    }

    public function removeFile(): void
    {
        $this->archivo = null;
        $this->resetReviewState();
        $this->stage = 'initial';
    }

    public function replaceFile(): void
    {
        $this->removeFile();
    }

    public function reviewFile(): void
    {
        abort_unless($this->canImport(), 403);

        $upload = $this->resolveUploadedCsv();

        if ($upload === null) {
            $this->analysisError = 'Seleccione un archivo CSV antes de revisarlo.';
            $this->stage = 'validation_error';

            return;
        }

        if (! $this->hasCsvExtension($upload['original_name'])) {
            $this->upload = $upload;
            $this->analysisError = 'El archivo seleccionado debe tener extension .csv.';
            $this->stage = 'validation_error';

            return;
        }

        $this->stage = 'validating';
        $this->analysisError = null;
        $this->upload = $upload;

        try {
            $this->analysis = app(ChilquintaCsvImporter::class)->analyze($upload['absolute_path'], $this->importType());
        } catch (Throwable $e) {
            Log::warning('No se pudo revisar archivo CSV Chilquinta', [
                'tipo' => $this->importType(),
                'message' => $e->getMessage(),
            ]);

            $this->analysis = null;
            $this->analysisError = 'No pudimos leer el archivo. Revise que sea un CSV valido, con encabezados en la primera fila y separado por coma o punto y coma.';
            $this->stage = 'validation_error';

            return;
        }

        if (! ($this->analysis['can_import'] ?? false)) {
            $missing = collect($this->analysis['missing_columns'] ?? [])
                ->intersect(['caso', 'nis'])
                ->map(fn (string $field): string => ChilquintaCsvImporter::fieldLabel($field))
                ->implode(', ');

            $this->analysisError = $missing !== ''
                ? "Faltan columnas obligatorias para interpretar el archivo: {$missing}."
                : 'Faltan columnas obligatorias para interpretar el archivo.';
            $this->stage = 'validation_error';

            return;
        }

        if (($this->analysis['total_rows'] ?? 0) === 0) {
            $this->analysisError = 'El archivo no contiene filas para importar.';
            $this->stage = 'validation_error';

            return;
        }

        $this->stage = 'review';
    }

    public function goToIncidents(): void
    {
        if ($this->analysis === null) {
            return;
        }

        $this->stage = 'incidents';
    }

    public function goToConfirmation(): void
    {
        if (! $this->canMoveToConfirmation()) {
            return;
        }

        $this->stage = 'confirmation';
    }

    public function backToReview(): void
    {
        if ($this->analysis === null) {
            $this->stage = $this->upload === null ? 'initial' : 'selected';

            return;
        }

        $this->stage = 'review';
    }

    public function importRecords(): void
    {
        abort_unless($this->canImport(), 403);

        if ($this->importing || $this->stage !== 'confirmation' || ! $this->canMoveToConfirmation()) {
            return;
        }

        $this->importing = true;
        $this->stage = 'importing';

        try {
            $staged = $this->stageUploadedCsv();
            $result = app(ChilquintaCsvImporter::class)->import(
                path: $staged['absolute_path'],
                type: $this->importType(),
                userId: auth()->id(),
                originalFilename: $staged['original_name'],
                storedFilename: $staged['relative_path'],
            );

            $this->result = $result;
            $this->historialId = $result['historial'] instanceof ImportacionHistorial
                ? $result['historial']->id
                : null;
            $this->stage = ($result['errors'] ?? 0) > 0 || ($result['skipped'] ?? 0) > 0
                ? 'partial_success'
                : 'success';

            Notification::make()
                ->title($this->stage === 'success' ? 'Importacion finalizada' : 'Importacion parcial finalizada')
                ->body($this->stage === 'success'
                    ? 'Los registros fueron importados correctamente.'
                    : 'Se procesaron las filas validas y quedaron incidencias para revisar.')
                ->status($this->stage === 'success' ? 'success' : 'warning')
                ->send();
        } catch (Throwable $e) {
            Log::error('No se pudo ejecutar importacion CSV Chilquinta', [
                'tipo' => $this->importType(),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->result = null;
            $this->stage = 'failed';

            Notification::make()
                ->title('No se pudo importar el archivo')
                ->body('Revise el archivo y vuelva a intentarlo. Si el problema persiste, consulte el historial de importaciones.')
                ->danger()
                ->send();
        } finally {
            $this->importing = false;
        }
    }

    public function startNewImport(): void
    {
        $this->archivo = null;
        $this->resetReviewState();
        $this->stage = 'initial';
    }

    public function downloadTemplate()
    {
        abort_unless($this->canImport(), 403);

        return response()->download(storage_path($this->templatePath()), $this->templateDownloadName());
    }

    public function downloadAnalysisErrors()
    {
        abort_unless($this->canImport(), 403);

        $errors = $this->analysis['incidents'] ?? [];

        if ($errors === []) {
            return null;
        }

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['fila', 'campo', 'valor_recibido', 'motivo', 'como_corregir']);

        foreach ($errors as $error) {
            fputcsv($handle, [
                $error['row'] ?? '',
                $error['field'] ?? '',
                $error['value'] ?? '',
                $error['message'] ?? '',
                $error['correction'] ?? '',
            ]);
        }

        rewind($handle);
        $contents = stream_get_contents($handle);
        fclose($handle);

        return response()->streamDownload(
            fn () => print $contents,
            'incidencias_'.$this->importType().'_'.now()->format('Ymd_His').'.csv',
            ['Content-Type' => 'text/csv; charset=UTF-8'],
        );
    }

    public function downloadResultErrors()
    {
        abort_unless($this->canImport(), 403);

        $path = $this->result['error_report_path'] ?? null;

        if (! is_string($path) || ! Storage::disk('local')->exists($path)) {
            return null;
        }

        return response()->download(Storage::disk('local')->path($path), 'errores_importacion_'.$this->importType().'.csv');
    }

    public function toggleIssuesOnly(): void
    {
        $this->showOnlyIssues = ! $this->showOnlyIssues;
    }

    public function getTitle(): string
    {
        return 'Importar '.$this->moduleName();
    }

    public function canImport(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->hasRole('administrador') || $user?->can(static::$importPermissionName));
    }

    public function canMoveToConfirmation(): bool
    {
        return $this->analysis !== null
            && ($this->analysis['can_import'] ?? false)
            && (($this->analysis['ready_rows'] ?? 0) > 0)
            && ! in_array($this->stage, ['importing', 'success', 'partial_success'], true);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'moduleName' => $this->moduleName(),
            'type' => $this->importType(),
            'templateDownloadName' => $this->templateDownloadName(),
            'visibleColumns' => $this->visibleColumns(),
            'backUrl' => $this->backUrl(),
            'historyUrl' => ImportacionHistorialResource::getUrl('index', [
                'tableFilters' => [
                    'tipo_importacion' => ['value' => $this->historyType()],
                ],
            ]),
            'detailUrl' => $this->historialId
                ? ImportacionHistorialResource::getUrl('view', ['record' => $this->historialId])
                : null,
        ];
    }

    private function resetReviewState(): void
    {
        $this->upload = null;
        $this->analysis = null;
        $this->result = null;
        $this->historialId = null;
        $this->analysisError = null;
        $this->showOnlyIssues = false;
        $this->importing = false;
    }

    /**
     * @return array{absolute_path:string,original_name:string,size:int}|null
     */
    private function resolveUploadedCsv(): ?array
    {
        $archivo = $this->archivo;

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

        return null;
    }

    /**
     * @return array{absolute_path:string,relative_path:string,original_name:string,size:int}
     */
    private function stageUploadedCsv(): array
    {
        $upload = $this->resolveUploadedCsv();

        if ($upload === null) {
            throw new \InvalidArgumentException('No se encontro el archivo cargado para esta importacion.');
        }

        $safeName = Str::of(pathinfo($upload['original_name'], PATHINFO_FILENAME))
            ->ascii()
            ->slug('_')
            ->limit(80, '')
            ->append('.csv')
            ->toString();
        $relativePath = "imports/{$this->importType()}/".(string) Str::uuid()."/{$safeName}";
        $stream = fopen($upload['absolute_path'], 'rb');

        if ($stream === false) {
            throw new \InvalidArgumentException('No se pudo abrir el archivo cargado.');
        }

        Storage::disk('local')->put($relativePath, $stream);
        fclose($stream);

        if (Storage::disk('local')->size($relativePath) !== $upload['size']) {
            throw new \RuntimeException('El archivo almacenado no coincide con el archivo cargado.');
        }

        return [
            'absolute_path' => Storage::disk('local')->path($relativePath),
            'relative_path' => $relativePath,
            'original_name' => $upload['original_name'],
            'size' => $upload['size'],
        ];
    }

    private function hasCsvExtension(string $filename): bool
    {
        return Str::lower(pathinfo($filename, PATHINFO_EXTENSION)) === 'csv';
    }

    private function historyType(): string
    {
        return $this->importType() === ChilquintaCsvImporter::TYPE_QUERELLA ? 'querellas' : 'convenios';
    }
}
