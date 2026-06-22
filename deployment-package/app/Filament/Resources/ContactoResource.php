<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactoResource\Pages;
use App\Models\Contacto;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ContactoResource extends Resource
{
    protected static ?string $model = Contacto::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|\UnitEnum|null $navigationGroup = 'Gestión Legal';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'contacto';

    protected static ?string $pluralModelLabel = 'contactos';

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('contactos.ver') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('contactos.crear') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('contactos.editar') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('contactos.eliminar') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('contactos.eliminar') ?? false;
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()?->can('contactos.ver') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Contacto')
                    ->tabs([
                        Tab::make('General')
                            ->schema([
                                DateTimePicker::make('fecha'),
                                Toggle::make('importante'),
                                Textarea::make('comentario')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                        Tab::make('Relaciones / Archivos')
                            ->schema([
                                Select::make('convenio_id')
                                    ->label('Convenio')
                                    ->relationship('convenio', 'numero')
                                    ->searchable()
                                    ->preload()
                                    ->native(false),
                                Select::make('user_id')
                                    ->label('Usuario')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->native(false),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('convenio.numero')
                    ->label('Convenio')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('fecha')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('importante')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('comentario')
                    ->searchable()
                    ->limit(60),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('fecha', 'desc')
            ->paginated([10, 25, 50]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactos::route('/'),
            'create' => Pages\CreateContacto::route('/create'),
            'edit' => Pages\EditContacto::route('/{record}/edit'),
        ];
    }
}
