<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProcesoResource\Pages;
use App\Models\Convenio;
use App\Models\Proceso;
use App\Models\Querella;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ProcesoResource extends Resource
{
    protected static ?string $model = Proceso::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|\UnitEnum|null $navigationGroup = 'Gestión Legal';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'proceso';

    protected static ?string $pluralModelLabel = 'procesos';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('procesos.ver') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('procesos.crear') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('procesos.editar') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('procesos.eliminar') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('procesos.eliminar') ?? false;
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()?->can('procesos.ver') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Proceso')
                    ->tabs([
                        Tab::make('General')
                            ->schema([
                                TextInput::make('nombre')
                                    ->maxLength(255),
                                TextInput::make('tipo')
                                    ->maxLength(255),
                                DateTimePicker::make('fecha'),
                                Textarea::make('descripcion')
                                    ->label('Descripción')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                        Tab::make('Relaciones / Archivos')
                            ->schema([
                                Select::make('procesable_type')
                                    ->label('Tipo de origen')
                                    ->options([
                                        Convenio::class => 'Convenio',
                                        Querella::class => 'Querella',
                                    ])
                                    ->native(false),
                                TextInput::make('procesable_id')
                                    ->label('ID de origen')
                                    ->numeric(),
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
                                TextInput::make('checksum_archivo')
                                    ->label('Checksum archivo')
                                    ->maxLength(64),
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
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tipo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fecha')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('procesable_type')
                    ->label('Tipo de origen')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('procesable_id')
                    ->label('ID de origen')
                    ->sortable(),
                TextColumn::make('checksum_archivo')
                    ->label('Checksum')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListProcesos::route('/'),
            'create' => Pages\CreateProceso::route('/create'),
            'edit' => Pages\EditProceso::route('/{record}/edit'),
        ];
    }
}
