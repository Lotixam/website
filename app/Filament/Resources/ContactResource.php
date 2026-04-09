<?php

namespace App\Filament\Resources;

use App\Enums\ContactType;
use App\Filament\Resources\ContactResource\Pages;
use App\Models\Contact;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Commercial';

    protected static ?string $modelLabel = 'Contact';

    protected static ?string $pluralModelLabel = 'Contacts';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Identité')
                    ->columns(2)
                    ->schema([
                        Select::make('type')->label('Type')
                            ->options(collect(ContactType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()]))
                            ->default('other')->required(),
                        TextInput::make('company')->label('Société'),
                        TextInput::make('first_name')->label('Prénom')->required(),
                        TextInput::make('last_name')->label('Nom')->required(),
                        TextInput::make('email')->label('Email')->email(),
                        TextInput::make('phone')->label('Téléphone')->tel(),
                    ]),
                Section::make('Adresse')
                    ->columns(3)
                    ->schema([
                        TextInput::make('address')->label('Adresse')->columnSpan(3),
                        TextInput::make('postal_code')->label('Code postal')->maxLength(10),
                        TextInput::make('city')->label('Ville'),
                    ])
                    ->collapsible(),
                Section::make('Notes')
                    ->schema([RichEditor::make('notes')->label('Notes')])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')->label('Nom')->searchable(['first_name', 'last_name'])->sortable(['last_name']),
                Tables\Columns\TextColumn::make('type')->label('Type')->badge()->formatStateUsing(fn (ContactType $state) => $state->label()),
                Tables\Columns\TextColumn::make('company')->label('Société')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('Téléphone'),
                Tables\Columns\TextColumn::make('city')->label('Ville')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('last_name')
            ->filters([
                Tables\Filters\SelectFilter::make('type')->label('Type')
                    ->options(collect(ContactType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()])),
            ])
            ->actions([Actions\EditAction::make()])
            ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
