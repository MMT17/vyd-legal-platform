<?php

namespace App\Filament\Resources\QuerellaResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ProcesosRelationManager extends RelationManager
{
    protected static string $relationship = 'procesos';

    protected static ?string $title = 'Procesos';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()?->can('procesos.ver') ?? false;
    }

    protected function canViewAny(): bool
    {
        return auth()->user()?->can('procesos.ver') ?? false;
    }

    protected function canCreate(): bool
    {
        return auth()->user()?->can('procesos.crear') ?? false;
    }

    protected function canEdit(Model $record): bool
    {
        return auth()->user()?->can('procesos.editar') ?? false;
    }

    protected function canDelete(Model $record): bool
    {
        return auth()->user()?->can('procesos.eliminar') ?? false;
    }

    protected function canDeleteAny(): bool
    {
        return auth()->user()?->can('procesos.eliminar') ?? false;
    }

    protected function canView(Model $record): bool
    {
        return auth()->user()?->can('procesos.ver') ?? false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('nombre')
                    ->maxLength(255),
                TextInput::make('tipo')
                    ->maxLength(255),
                DateTimePicker::make('fecha'),
                FileUpload::make('archivo_path')
                    ->label('Archivo')
                    ->disk('public')
                    ->directory('procesos')
                    ->acceptedFileTypes([
                        'application/pdf',
                        'image/jpeg',
                        'image/png',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ])
                    ->maxSize(51200)
                    ->downloadable()
                    ->openable()
                    ->preserveFilenames()
                    ->helperText('Para servir archivos públicos debe ejecutarse: php artisan storage:link'),
                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->columns([
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tipo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fecha')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(60)
                    ->searchable(),
                TextColumn::make('archivo_path')
                    ->label('Ruta de archivo')
                    ->searchable()
                    ->toggleable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('fecha', 'desc')
            ->paginated([10, 25]);
    }
}
