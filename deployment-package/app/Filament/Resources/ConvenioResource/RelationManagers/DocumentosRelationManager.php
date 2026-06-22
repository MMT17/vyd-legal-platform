<?php

namespace App\Filament\Resources\ConvenioResource\RelationManagers;

use App\Models\Documento;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DocumentosRelationManager extends RelationManager
{
    protected static string $relationship = 'documentos';

    protected static ?string $title = 'Documentos';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()?->can('documentos.ver') ?? false;
    }

    protected function canViewAny(): bool
    {
        return auth()->user()?->can('documentos.ver') ?? false;
    }

    protected function canCreate(): bool
    {
        return auth()->user()?->can('documentos.crear') ?? false;
    }

    protected function canEdit(Model $record): bool
    {
        return auth()->user()?->can('documentos.editar') ?? false;
    }

    protected function canDelete(Model $record): bool
    {
        return auth()->user()?->can('documentos.eliminar') ?? false;
    }

    protected function canDeleteAny(): bool
    {
        return auth()->user()?->can('documentos.eliminar') ?? false;
    }

    protected function canView(Model $record): bool
    {
        return auth()->user()?->can('documentos.ver') ?? false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
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
                FileUpload::make('archivo_path')
                    ->label('Archivo')
                    ->disk('public')
                    ->directory('documentos/convenios')
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
                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
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
                    ->getStateUsing(fn ($record): bool => filled($record->archivo_path)),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Nuevo documento'),
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
            ->paginated([10, 25]);
    }
}
