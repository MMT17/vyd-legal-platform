<?php

namespace App\Filament\Cliente\Resources;

use App\Filament\Cliente\Resources\DocumentosClienteResource\Pages;
use App\Models\Documento;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DocumentosClienteResource extends Resource
{
    protected static ?string $model = Documento::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    protected static ?string $navigationLabel = 'Documentos';

    protected static ?string $modelLabel = 'documento';

    protected static ?string $pluralModelLabel = 'Documentos';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'nombre';

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
                TextInput::make('nombre'),
                TextInput::make('tipo_documento')->label('Tipo documento'),
                DateTimePicker::make('fecha'),
                Placeholder::make('documento_relacionado')
                    ->label('Relacionado con')
                    ->content(fn (Documento $record): string => self::relatedLabel($record)),
                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tipo_documento')
                    ->label('Tipo documento')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fecha')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('documento_relacionado')
                    ->label('Documento relacionado')
                    ->getStateUsing(fn (Documento $record): string => self::relatedLabel($record)),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Ver'),
                self::downloadAction(),
            ])
            ->toolbarActions([])
            ->defaultSort('fecha', 'desc')
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocumentosCliente::route('/'),
            'view' => Pages\ViewDocumentoCliente::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('documentable');
    }

    public static function downloadAction(): Action
    {
        return Action::make('descargar')
            ->label('Descargar')
            ->icon(Heroicon::OutlinedArrowDownTray)
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
                && (auth()->user()?->can('documentos.descargar') ?? false));
    }

    private static function relatedLabel(Documento $documento): string
    {
        $related = $documento->documentable;

        if (! $related) {
            return 'Sin relación';
        }

        $label = $related->numero ?? $related->nombre ?? "#{$related->getKey()}";

        return class_basename($related) . ' ' . $label;
    }
}
