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
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, mixed $state, callable $set): void {
                        if ($operation === 'create') {
                            $set('slug', Str::slug((string) $state));
                        }
                    }),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('position')
                    ->label('Cargo / especialidad principal')
                    ->maxLength(255),
                TagsInput::make('specialties')
                    ->label('Áreas de práctica')
                    ->placeholder('Agregar área')
                    ->suggestions([
                        'Derecho Administrativo',
                        'Derecho Corporativo',
                        'Derecho Civil',
                        'Derecho Penal',
                        'Derecho Concursal',
                        'Derecho Laboral',
                        'Derecho de Familia',
                        'Derecho del Trabajo',
                        'Regulación Eléctrica',
                        'Litigación',
                        'Propiedad Intelectual',
                        'Compras Públicas',
                        'Juzgados de Policía Local',
                        'Negociaciones',
                    ])
                    ->columnSpanFull(),
                TagsInput::make('education')
                    ->label('Educación')
                    ->placeholder('Agregar antecedente académico')
                    ->columnSpanFull(),
                TagsInput::make('experience')
                    ->label('Experiencia profesional')
                    ->placeholder('Agregar experiencia')
                    ->columnSpanFull(),
                TagsInput::make('activities')
                    ->label('Actividades académicas y profesionales')
                    ->placeholder('Agregar actividad')
                    ->columnSpanFull(),
                FileUpload::make('photo_path')
                    ->label('Foto')
                    ->image()
                    ->disk('public')
                    ->directory('cms/equipo')
                    ->downloadable()
                    ->openable(),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),
                TextInput::make('linkedin_url')
                    ->label('LinkedIn')
                    ->url()
                    ->maxLength(255),
                Toggle::make('is_partner')
                    ->label('Es socio')
                    ->default(false),
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
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('position')
                    ->label('Cargo')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_partner')
                    ->label('Socio')
                    ->boolean()
                    ->sortable(),
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
