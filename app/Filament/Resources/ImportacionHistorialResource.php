<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImportacionHistorialResource\Pages;
use App\Models\ImportacionHistorial;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ImportacionHistorialResource extends Resource
{
    protected static ?string $model = ImportacionHistorial::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownTray;

    protected static string|\UnitEnum|null $navigationGroup = 'Sistema';

    protected static ?string $navigationLabel = 'Historial de Importaciones';

    protected static ?string $modelLabel = 'historial de importación';

    protected static ?string $pluralModelLabel = 'historial de importaciones';

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->can('auditoria.ver')
            || $user?->can('convenios.importar')
            || $user?->can('querellas.importar'));
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function canView(Model $record): bool
    {
        return $record instanceof ImportacionHistorial && self::canAccessImportHistory($record->tipo_importacion);
    }

    public static function canAccessImportHistory(?string $type): bool
    {
        $user = auth()->user();

        if ($user?->can('auditoria.ver')) {
            return true;
        }

        return match ($type) {
            'convenios' => (bool) $user?->can('convenios.importar'),
            'querellas' => (bool) $user?->can('querellas.importar'),
            default => false,
        };
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user?->can('auditoria.ver')) {
            return $query;
        }

        $types = [];

        if ($user?->can('convenios.importar')) {
            $types[] = 'convenios';
        }

        if ($user?->can('querellas.importar')) {
            $types[] = 'querellas';
        }

        return $query->whereIn('tipo_importacion', $types);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tipo_importacion')
                    ->label('Modulo')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'convenios' => 'Convenios',
                        'querellas' => 'Querellas',
                        default => $state ? str($state)->replace('_', ' ')->title()->toString() : 'Sin modulo',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('archivo_original')
                    ->label('Archivo')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('total_registros')
                    ->label('Total')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('registros_validos')
                    ->label('Validos')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('creados')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('actualizados')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('duplicados')
                    ->label('Omitidos')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('errores')
                    ->label('Fallidos')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('estado')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pendiente' => 'Pendiente',
                        'validado' => 'Validado',
                        'procesando' => 'Procesando',
                        'completado' => 'Completado',
                        'con_errores' => 'Con errores',
                        'fallido' => 'Fallido',
                        default => $state ? str($state)->replace('_', ' ')->title()->toString() : 'Sin estado',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'pendiente' => 'gray',
                        'validado' => 'info',
                        'procesando' => 'warning',
                        'completado' => 'success',
                        'con_errores' => 'warning',
                        'fallido' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('completado_at')
                    ->label('Completado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tipo_importacion')
                    ->label('Modulo')
                    ->options([
                        'convenios' => 'Convenios',
                        'querellas' => 'Querellas',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Ver detalle'),
                Action::make('descargarErrores')
                    ->label('Errores')
                    ->icon(Heroicon::ArrowDownTray)
                    ->visible(fn (ImportacionHistorial $record): bool => filled($record->reporte_errores))
                    ->action(fn (ImportacionHistorial $record) => response()->download(
                        Storage::disk('local')->path($record->reporte_errores),
                        'errores_importacion_'.$record->id.'.csv',
                    )),
            ])
            ->toolbarActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListImportacionHistorial::route('/'),
            'view' => Pages\ViewImportacionHistorial::route('/{record}'),
        ];
    }
}
