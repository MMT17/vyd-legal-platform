<?php

namespace App\Filament\Resources\ConvenioResource\RelationManagers;

use App\Models\Contacto;
use App\Models\Documento;
use App\Models\Proceso;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;

class HistorialRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    protected static ?string $title = 'Historial';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()?->can('auditoria.ver') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->activityQuery())
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('causer.name')
                    ->label('Usuario')
                    ->formatStateUsing(fn (?string $state): string => $state ?: 'Sistema')
                    ->searchable(),
                TextColumn::make('event')
                    ->label('Acción')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $this->formatEvent($state))
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Descripción')
                    ->searchable()
                    ->wrap(),
            ])
            ->recordActions([])
            ->toolbarActions([])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }

    private function activityQuery(): Builder
    {
        $owner = $this->getOwnerRecord();
        $procesoIds = $owner->procesos()->pluck('id')->all();
        $documentoIds = $owner->documentos()->pluck('id')->all();
        $contactoIds = $owner->contactos()->pluck('id')->all();

        return Activity::query()
            ->select(['id', 'description', 'event', 'causer_type', 'causer_id', 'subject_type', 'subject_id', 'created_at'])
            ->with('causer')
            ->where(function (Builder $query) use ($owner, $procesoIds, $documentoIds, $contactoIds): void {
                $query
                    ->where(fn (Builder $query) => $query
                        ->where('subject_type', $owner->getMorphClass())
                        ->where('subject_id', $owner->getKey()))
                    ->orWhere(fn (Builder $query) => $query
                        ->where('subject_type', app(Proceso::class)->getMorphClass())
                        ->whereIn('subject_id', $procesoIds ?: [0]))
                    ->orWhere(fn (Builder $query) => $query
                        ->where('subject_type', app(Documento::class)->getMorphClass())
                        ->whereIn('subject_id', $documentoIds ?: [0]))
                    ->orWhere(fn (Builder $query) => $query
                        ->where('subject_type', app(Contacto::class)->getMorphClass())
                        ->whereIn('subject_id', $contactoIds ?: [0]));
            })
            ->latest('created_at')
            ->limit(20);
    }

    private function formatEvent(?string $event): string
    {
        return match ($event) {
            'created' => 'Creado',
            'updated' => 'Actualizado',
            'deleted' => 'Eliminado',
            'imported' => 'Importado',
            'downloaded' => 'Descargado',
            default => $event ?: 'Actividad',
        };
    }

}
