<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PracticeAreaResource\Pages;
use App\Models\Cms\PracticeArea;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PracticeAreaResource extends Resource
{
    protected static ?string $model = PracticeArea::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static string|\UnitEnum|null $navigationGroup = 'Sitio Público';

    protected static ?string $navigationLabel = 'Áreas de práctica';

    protected static ?string $modelLabel = 'área de práctica';

    protected static ?string $pluralModelLabel = 'Áreas de práctica';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'title';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('administrador') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasRole('administrador') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->hasRole('administrador') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->hasRole('administrador') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->hasRole('administrador') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('icon')
                    ->label('Icono')
                    ->maxLength(255),
                FileUpload::make('image_path')
                    ->label('Imagen')
                    ->image()
                    ->disk('public')
                    ->directory('cms/areas')
                    ->downloadable()
                    ->openable(),
                Textarea::make('excerpt')
                    ->label('Resumen')
                    ->rows(3)
                    ->columnSpanFull(),
                RichEditor::make('content')
                    ->label('Contenido')
                    ->columnSpanFull(),
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
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
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPracticeAreas::route('/'),
            'create' => Pages\CreatePracticeArea::route('/create'),
            'edit' => Pages\EditPracticeArea::route('/{record}/edit'),
        ];
    }
}
