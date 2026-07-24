<?php

namespace App\Filament\Resources\ImportacionHistorialResource\Pages;

use App\Filament\Resources\ImportacionHistorialResource;
use App\Models\ImportacionHistorial;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;

class ViewImportacionHistorial extends ViewRecord
{
    protected static string $resource = ImportacionHistorialResource::class;

    protected string $view = 'filament.resources.importacion-historial.view';

    protected function authorizeAccess(): void
    {
        abort_unless(ImportacionHistorialResource::canView($this->getRecord()), 403);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('descargarErrores')
                ->label('Descargar errores')
                ->icon(Heroicon::ArrowDownTray)
                ->visible(fn (): bool => filled($this->getRecord()->reporte_errores))
                ->action(fn () => response()->download(
                    Storage::disk('local')->path($this->getRecord()->reporte_errores),
                    'errores_importacion_'.$this->getRecord()->id.'.csv',
                )),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        unset($data['detalles']);

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        /** @var ImportacionHistorial $record */
        $record = $this->getRecord();

        return [
            'record' => $record,
            'incidents' => collect($record->detalles['errores'] ?? [])->take(50)->values()->all(),
            'moduleName' => match ($record->tipo_importacion) {
                'convenios' => 'Convenios',
                'querellas' => 'Querellas',
                default => 'Importacion',
            },
        ];
    }
}
