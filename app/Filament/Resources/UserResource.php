<?php

namespace App\Filament\Resources;

use App\Enums\Gender;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    protected static ?string $modelLabel = 'Membre';

    protected static ?string $pluralModelLabel = 'Membres';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    /**
     * @param  array<string, mixed>  $profile
     */
    public static function displayNameFromProfile(array $profile, ?string $emailFallback = null): string
    {
        $first = trim((string) ($profile['first_name'] ?? ''));
        $last = trim((string) ($profile['last_name'] ?? ''));
        $full = trim("{$first} {$last}");

        if ($full !== '') {
            return $full;
        }

        return ($emailFallback !== null && $emailFallback !== '')
            ? $emailFallback
            : 'Membre';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('username')
                    ->label('Identifiant')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                FileUpload::make('avatar')
                    ->label('Photo de profil')
                    ->image()
                    ->avatar()
                    ->directory('avatars')
                    ->disk('public')
                    ->maxSize(2048)
                    ->imageEditor()
                    ->nullable(),
                TextInput::make('password')
                    ->label('Mot de passe')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
                Fieldset::make('Identité')
                    ->relationship('profile')
                    ->columns(2)
                    ->schema([
                        TextInput::make('first_name')
                            ->label('Prénom')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),
                        Select::make('gender')
                            ->label('Genre')
                            ->options(Gender::class)
                            ->native(false)
                            ->columnSpanFull(),
                        TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->maxLength(32)
                            ->columnSpanFull(),
                    ]),
                Select::make('role')
                    ->label('Rôle')
                    ->options([
                        'admin' => 'Administrateur',
                        'collaborator' => 'Collaborateur',
                        'client' => 'Client',
                    ])
                    ->required()
                    ->default('client')
                    ->live()
                    ->afterStateHydrated(function (Select $component, ?User $record) {
                        if ($record) {
                            $component->state($record->roles->first()?->name);
                        }
                    }),
                Select::make('partner_id')
                    ->label('Entreprise')
                    ->relationship('partner', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->required(fn (Get $get) => in_array((string) $get('role'), ['client', 'collaborator'], true))
                    ->visible(fn (Get $get) => in_array((string) $get('role'), ['client', 'collaborator'], true))
                    ->helperText('Obligatoire pour tout client ou collaborateur (financeur, apporteur d’affaires, constructeur, interne partenaire, etc.). Les administrateurs Lotixam n’ont pas d’entreprise rattachée.'),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['profile', 'partner']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->width(40),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $q) use ($search): void {
                            $q->where('users.name', 'like', "%{$search}%")
                                ->orWhereHas('profile', function (Builder $pq) use ($search): void {
                                    $pq->where('first_name', 'like', "%{$search}%")
                                        ->orWhere('last_name', 'like', "%{$search}%");
                                });
                        });
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('profile.phone')
                    ->label('Téléphone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('username')
                    ->label('Identifiant')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('partner.name')
                    ->label('Entreprise')
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Rôle')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'admin' => 'Administrateur',
                        'collaborator' => 'Collaborateur',
                        'client' => 'Client',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'admin' => 'danger',
                        'collaborator' => 'info',
                        'client' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Rôle')
                    ->options([
                        'admin' => 'Administrateur',
                        'collaborator' => 'Collaborateur',
                        'client' => 'Client',
                    ])
                    ->query(fn (Builder $query, array $data) => $data['value']
                        ? $query->role($data['value'])
                        : $query
                    ),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
