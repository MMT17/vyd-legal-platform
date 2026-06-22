<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\Cms\ContactMessage;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|\UnitEnum|null $navigationGroup = 'Sitio Público';

    protected static ?string $navigationLabel = 'Mensajes de contacto';

    protected static ?string $modelLabel = 'mensaje de contacto';

    protected static ?string $pluralModelLabel = 'Mensajes de contacto';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'subject';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('administrador') ?? false;
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
        return auth()->user()?->hasRole('administrador') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->hasRole('administrador') ?? false;
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()?->hasRole('administrador') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->disabled(),
                TextInput::make('email')
                    ->disabled(),
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->disabled(),
                TextInput::make('subject')
                    ->label('Asunto')
                    ->disabled(),
                Textarea::make('message')
                    ->label('Mensaje')
                    ->rows(8)
                    ->disabled()
                    ->columnSpanFull(),
                TextInput::make('created_at')
                    ->label('Recibido')
                    ->disabled(),
                TextInput::make('read_at')
                    ->label('Leído')
                    ->disabled(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('read_at')
                    ->label('Leído')
                    ->boolean()
                    ->getStateUsing(fn (ContactMessage $record): bool => filled($record->read_at)),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('subject')
                    ->label('Asunto')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label('Recibido')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Ver'),
                Action::make('marcar_leido')
                    ->label('Marcar leído')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->action(fn (ContactMessage $record) => $record->update(['read_at' => now()]))
                    ->visible(fn (ContactMessage $record): bool => blank($record->read_at)),
                DeleteAction::make()
                    ->label('Eliminar'),
            ])
            ->toolbarActions([])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'view' => Pages\ViewContactMessage::route('/{record}'),
        ];
    }
}
