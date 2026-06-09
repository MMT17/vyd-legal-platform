<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuerellaResource\Pages;
use App\Filament\Resources\QuerellaResource\RelationManagers\DocumentosRelationManager;
use App\Filament\Resources\QuerellaResource\RelationManagers\HistorialRelationManager;
use App\Filament\Resources\QuerellaResource\RelationManagers\ProcesosRelationManager;
use App\Models\Querella;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class QuerellaResource extends Resource
{
    protected static ?string $model = Querella::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static string|\UnitEnum|null $navigationGroup = 'Gestión Legal';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'querella';

    protected static ?string $pluralModelLabel = 'querellas';

    protected static ?string $recordTitleAttribute = 'numero';

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('querellas.listar') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasRole('administrador') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('querellas.editar') ?? false;
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
        return auth()->user()?->can('querellas.ver') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Querella')
                    ->tabs([
                        Tab::make('General')
                            ->schema([
                                TextInput::make('numero')
                                    ->label('Número')
                                    ->maxLength(255),
                                TextInput::make('id_querella')
                                    ->label('ID querella')
                                    ->maxLength(255),
                                TextInput::make('ciudad')
                                    ->maxLength(255),
                                Select::make('estado')
                                    ->options([
                                        'en_tramitacion' => 'En tramitación',
                                        'terminada' => 'Terminada',
                                        'archivada' => 'Archivada',
                                    ])
                                    ->native(false),
                                DatePicker::make('fecha'),
                                DateTimePicker::make('fecha_cierre')
                                    ->label('Fecha de cierre'),
                                TextInput::make('documentacion_estado')
                                    ->label('Estado documentación')
                                    ->maxLength(100),
                                TextInput::make('abogado_responsable')
                                    ->label('Abogado responsable')
                                    ->maxLength(255),
                                TextInput::make('tribunal')
                                    ->maxLength(100),
                                TextInput::make('tipo_proceso')
                                    ->label('Tipo de proceso')
                                    ->maxLength(100),
                                Textarea::make('observacion')
                                    ->label('Observación')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                        Tab::make('Financiero')
                            ->schema([
                                TextInput::make('cnr_12_meses')
                                    ->label('CNR 12 meses')
                                    ->numeric(),
                                TextInput::make('cnr_fuera_ventana')
                                    ->label('CNR fuera ventana')
                                    ->numeric(),
                                TextInput::make('deuda_total')
                                    ->label('Deuda total')
                                    ->numeric(),
                                TextInput::make('acuerdo_extrajudicial')
                                    ->label('Acuerdo extrajudicial')
                                    ->numeric(),
                                TextInput::make('cuotas')
                                    ->numeric(),
                                TextInput::make('cnr_pagado_anterior')
                                    ->label('CNR pagado anterior')
                                    ->numeric(),
                            ])
                            ->columns(2),
                        Tab::make('Relaciones / Archivos')
                            ->schema([
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
                TextColumn::make('numero')
                    ->label('Número')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('id_querella')
                    ->label('ID querella')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ciudad')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('estado')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'en_tramitacion' => 'warning',
                        'terminada' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'en_tramitacion' => 'En tramitación',
                        'terminada' => 'Terminada',
                        default => $state ?: 'Sin estado',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
                TextColumn::make('deuda_total')
                    ->money('CLP')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('abogado_responsable')
                    ->label('Abogado responsable')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('tribunal')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('tipo_proceso')
                    ->label('Tipo')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->options([
                        'en_tramitacion' => 'En tramitación',
                        'terminada' => 'Terminada',
                        'archivada' => 'Archivada',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Ver'),
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
            ProcesosRelationManager::class,
            DocumentosRelationManager::class,
            HistorialRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuerellas::route('/'),
            'create' => Pages\CreateQuerella::route('/create'),
            'view' => Pages\ViewQuerella::route('/{record}'),
            'edit' => Pages\EditQuerella::route('/{record}/edit'),
        ];
    }
}
