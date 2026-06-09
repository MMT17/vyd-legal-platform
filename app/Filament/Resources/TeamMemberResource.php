<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamMemberResource\Pages;
use App\Models\Cms\TeamMember;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TeamMemberResource extends Resource
{
    protected static ?string $model = TeamMember::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|\UnitEnum|null $navigationGroup = 'Sitio Público';

    protected static ?string $navigationLabel = 'Equipo';

    protected static ?string $modelLabel = 'integrante';

    protected static ?string $pluralModelLabel = 'Equipo';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

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
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                TextInput::make('position')
                    ->label('Cargo')
                    ->maxLength(255),
                FileUpload::make('photo_path')
                    ->label('Foto')
                    ->image()
                    ->disk('public')
                    ->directory('cms/equipo')
                    ->downloadable()
                    ->openable(),
                TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->maxLength(255),
                TextInput::make('linkedin_url')
                    ->label('LinkedIn')
                    ->url()
                    ->maxLength(255),
                RichEditor::make('bio')
                    ->label('Biografía')
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('position')
                    ->label('Cargo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('Activo')
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
            'index' => Pages\ListTeamMembers::route('/'),
            'create' => Pages\CreateTeamMember::route('/create'),
            'edit' => Pages\EditTeamMember::route('/{record}/edit'),
        ];
    }
}
