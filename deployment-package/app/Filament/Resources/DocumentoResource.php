<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentoResource\Pages;
use App\Models\Convenio;
use App\Models\Documento;
use App\Models\Querella;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DocumentoResource extends Resource
{
    protected static ?string $model = Documento::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    protected static string|\UnitEnum|null $navigationGroup = 'Gestión Legal';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'documento';

    protected static ?string $pluralModelLabel = 'documentos';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('documentos.ver') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('documentos.crear') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('documentos.editar') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('documentos.eliminar') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('documentos.eliminar') ?? false;
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()?->can('documentos.ver') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Documento')
                    ->tabs([
                        Tab::make('General')
                            ->schema([
                                TextInput::make('nombre')
                                    ->maxLength(255),
                                Select::make('tipo_documento')
                                    ->label('Tipo de documento')
                                    ->options([
                                        'contrato' => 'Contrato',
                                        'resolucion' => 'Resolución',
                                        'certificado' => 'Certificado',
                                        'comprobante' => 'Comprobante',
                                        'otro' => 'Otro',
                                    ])
                                    ->searchable()
                                    ->native(false),
                                DateTimePicker::make('fecha'),
                                Textarea::make('descripcion')
                                    ->label('Descripción')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                        Tab::make('Relaciones / Archivos')
                            ->schema([
                                FileUpload::make('archivo_path')
                                    ->label('Archivo')
                                    ->disk('public')
                                    ->directory(fn (Get $get): string => match ($get('documentable_type')) {
                                        Convenio::class => 'documentos/convenios',
                                        Querella::class => 'documentos/querellas',
                                        default => 'documentos',
                                    })
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
                TextColumn::make('tipo_documento')
                    ->label('Tipo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fecha')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('archivo_path')
                    ->label('Archivo')
                    ->boolean()
                    ->getStateUsing(fn (Documento $record): bool => filled($record->archivo_path)),
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
                ViewAction::make()
                    ->label('Ver'),
                Action::make('descargar')
                    ->label('Descargar')
                    ->icon(Heroicon::OutlinedDocumentArrowDown)
                    ->action(function (Documento $record) {
                        activity('documento')
                            ->causedBy(auth()->user())
                            ->performedOn($record)
                            ->event('downloaded')
                            ->log('Documento descargado');

                        return Storage::disk('public')->download(
                            $record->archivo_path,
                            $record->nombre_original ?: basename($record->archivo_path),
                        );
                    })
                    ->visible(fn (Documento $record): bool => filled($record->archivo_path)
                        && Storage::disk('public')->exists($record->archivo_path)
                        && (auth()->user()?->can('documentos.descargar') ?? false)),
                DeleteAction::make()
                    ->label('Eliminar'),
            ])
            ->toolbarActions([])
            ->emptyStateHeading('No hay documentos')
            ->emptyStateDescription('Cuando cargues documentos, aparecerán en esta lista.')
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
            'index' => Pages\ListDocumentos::route('/'),
            'create' => Pages\CreateDocumento::route('/create'),
            'edit' => Pages\EditDocumento::route('/{record}/edit'),
        ];
    }
}
