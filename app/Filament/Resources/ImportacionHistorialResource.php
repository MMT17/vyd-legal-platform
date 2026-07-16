<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImportacionHistorialResource\Pages;
use App\Models\ImportacionHistorial;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
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
        return auth()->user()?->can('auditoria.ver') ?? false;
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
        return auth()->user()?->can('auditoria.ver') ?? false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tipo_importacion')
                    ->label('Tipo')
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
                    ->numeric()
                    ->sortable(),
                TextColumn::make('errores')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('estado')
                    ->badge()
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
            ->recordActions([
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
        ];
    }
}
