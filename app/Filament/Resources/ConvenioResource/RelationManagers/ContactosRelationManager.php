<?php

namespace App\Filament\Resources\ConvenioResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ContactosRelationManager extends RelationManager
{
    protected static string $relationship = 'contactos';

    protected static ?string $title = 'Contactos';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()?->can('contactos.ver') ?? false;
    }

    protected function canViewAny(): bool
    {
        return auth()->user()?->can('contactos.ver') ?? false;
    }

    protected function canCreate(): bool
    {
        return auth()->user()?->can('contactos.crear') ?? false;
    }

    protected function canEdit(Model $record): bool
    {
        return auth()->user()?->can('contactos.editar') ?? false;
    }

    protected function canDelete(Model $record): bool
    {
        return auth()->user()?->can('contactos.eliminar') ?? false;
    }

    protected function canDeleteAny(): bool
    {
        return auth()->user()?->can('contactos.eliminar') ?? false;
    }

    protected function canView(Model $record): bool
    {
        return auth()->user()?->can('contactos.ver') ?? false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                DateTimePicker::make('fecha'),
                Select::make('user_id')
                    ->label('Usuario')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),
                Toggle::make('importante'),
                Textarea::make('comentario')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('comentario')
                    ->searchable()
                    ->limit(60),
                IconColumn::make('importante')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),
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
