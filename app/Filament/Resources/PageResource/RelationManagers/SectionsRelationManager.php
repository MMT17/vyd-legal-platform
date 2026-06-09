<?php

namespace App\Filament\Resources\PageResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    protected static ?string $title = 'Secciones';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('key')
                    ->label('Clave')
                    ->maxLength(255),
                TextInput::make('title')
                    ->label('Título')
                    ->maxLength(255),
                TextInput::make('subtitle')
                    ->label('Subtítulo')
                    ->maxLength(255),
                RichEditor::make('content')
                    ->label('Contenido')
                    ->columnSpanFull(),
                FileUpload::make('image_path')
                    ->label('Imagen')
                    ->image()
                    ->disk('public')
                    ->directory('cms/secciones')
                    ->downloadable()
                    ->openable(),
                TextInput::make('button_text')
                    ->label('Texto botón')
                    ->maxLength(255),
                TextInput::make('button_url')
                    ->label('URL botón')
                    ->url()
                    ->maxLength(255),
                TextInput::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Toggle::make('is_active')
                    ->label('Activa')
                    ->default(true),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('key')
                    ->label('Clave')
                    ->searchable(),
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Activa')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Nueva sección'),
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
            ->defaultSort('sort_order')
            ->paginated([10, 25]);
    }
}
