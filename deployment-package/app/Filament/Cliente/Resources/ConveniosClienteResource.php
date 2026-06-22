<?php

namespace App\Filament\Cliente\Resources;

use App\Filament\Cliente\Resources\ConveniosClienteResource\Pages;
use App\Filament\Cliente\Resources\ConveniosClienteResource\RelationManagers\DocumentosRelationManager;
use App\Models\Convenio;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ConveniosClienteResource extends Resource
{
    protected static ?string $model = Convenio::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Convenios';

    protected static ?string $modelLabel = 'convenio';

    protected static ?string $pluralModelLabel = 'Convenios';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'numero';

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('numero')->label('Número'),
                TextInput::make('estado'),
                TextInput::make('ciudad'),
                DatePicker::make('fecha'),
                DateTimePicker::make('fecha_cierre')->label('Fecha cierre'),
                TextInput::make('abogado_responsable')->label('Abogado responsable'),
                Textarea::make('observacion')->label('Observación')->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero')
                    ->label('Número')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('estado')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'firmado' => 'Firmado',
                        'en_negociacion' => 'En negociación',
                        'frustrado' => 'Frustrado',
                        default => $state ?: 'Sin estado',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'firmado' => 'success',
                        'en_negociacion' => 'warning',
                        'frustrado' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('ciudad')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
                TextColumn::make('abogado_responsable')
                    ->label('Abogado responsable')
                    ->searchable()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Ver'),
            ])
            ->toolbarActions([])
            ->defaultSort('fecha', 'desc')
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConveniosCliente::route('/'),
            'view' => Pages\ViewConvenioCliente::route('/{record}'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            DocumentosRelationManager::class,
        ];
    }
}
