<?php

namespace App\Filament\Cliente\Resources\ConveniosClienteResource\RelationManagers;

use App\Filament\Cliente\Resources\DocumentosClienteResource;
use App\Models\Documento;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DocumentosRelationManager extends RelationManager
{
    protected static string $relationship = 'documentos';

    protected static ?string $title = 'Documentos relacionados';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()?->can('documentos.ver') ?? false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->searchable()->sortable(),
                TextColumn::make('tipo_documento')->label('Tipo')->searchable()->sortable(),
                TextColumn::make('fecha')->dateTime()->sortable(),
            ])
            ->recordActions([
                Action::make('ver')
                    ->label('Ver')
                    ->icon(Heroicon::OutlinedEye)
                    ->url(fn (Documento $record): string => DocumentosClienteResource::getUrl('view', ['record' => $record])),
                Action::make('descargar')
                    ->label('Descargar')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->action(function (Documento $record) {
                        activity('documento')
                            ->causedBy(auth()->user())
                            ->performedOn($record)
                            ->event('downloaded')
                            ->log('Documento descargado');

                        return Storage::disk('public')->download(
                            $record->archivo_path,
                            $record->nombre_original ?: basename($record->archivo_path),
                        );
                    })
                    ->visible(fn (Documento $record): bool => filled($record->archivo_path)
                        && Storage::disk('public')->exists($record->archivo_path)
                        && (auth()->user()?->can('documentos.descargar') ?? false)),
            ])
            ->toolbarActions([])
            ->defaultSort('fecha', 'desc')
            ->paginated([10, 25]);
    }
}
