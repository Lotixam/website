<?php

namespace App\Filament\Resources;

use App\Enums\OperationStatus;
use App\Enums\OperationType;
use App\Filament\Resources\OperationResource\Pages;
use App\Filament\Resources\OperationResource\RelationManagers;
use App\Models\Contact;
use App\Models\Operation;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class OperationResource extends Resource
{
    protected static ?string $model = Operation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string|\UnitEnum|null $navigationGroup = 'Opérations';

    protected static ?string $modelLabel = 'Opération';

    protected static ?string $pluralModelLabel = 'Opérations';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informations générales')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom de l\'opération')
                            ->required()
                            ->maxLength(255),
                        Select::make('type')
                            ->label('Type de bien')
                            ->options(collect(OperationType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()]))
                            ->required(),
                        Select::make('status')
                            ->label('Statut')
                            ->options(collect(OperationStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                            ->default('prospection')
                            ->required(),
                        Select::make('seller_contact_id')
                            ->label('Vendeur')
                            ->relationship('seller', 'last_name')
                            ->getOptionLabelFromRecordUsing(fn (Contact $r) => "{$r->first_name} {$r->last_name}")
                            ->searchable(['first_name', 'last_name'])
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('first_name')->label('Prénom')->required(),
                                TextInput::make('last_name')->label('Nom')->required(),
                                TextInput::make('phone')->label('Téléphone'),
                                TextInput::make('email')->label('Email')->email(),
                            ])
                            ->createOptionUsing(function (array $data) {
                                $data['type'] = 'seller';
                                return Contact::create($data)->id;
                            }),
                    ]),
                Section::make('Localisation')
                    ->columns(3)
                    ->schema([
                        TextInput::make('address')->label('Adresse')->columnSpan(3),
                        TextInput::make('postal_code')->label('Code postal')->maxLength(10),
                        TextInput::make('city')->label('Ville'),
                    ]),
                Section::make('Données financières')
                    ->columns(2)
                    ->schema([
                        TextInput::make('total_surface')
                            ->label('Surface totale (m²)')
                            ->numeric()
                            ->suffix('m²'),
                        DatePicker::make('purchase_date')
                            ->label('Date d\'achat'),
                        TextInput::make('purchase_price')
                            ->label('Prix d\'achat')
                            ->numeric()
                            ->prefix('€'),
                        TextInput::make('estimated_resale_total')
                            ->label('Estimation revente totale')
                            ->numeric()
                            ->prefix('€'),
                    ]),
                Section::make('Notes')
                    ->schema([
                        RichEditor::make('notes')
                            ->label('Notes')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Opération')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn (OperationType $state) => $state->label())
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Ville')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (OperationStatus $state) => $state->label())
                    ->color(fn (OperationStatus $state) => $state->color()),
                Tables\Columns\TextColumn::make('purchase_price')
                    ->label('Prix d\'achat')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('lots_count')
                    ->label('Lots')
                    ->counts('lots')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Modifié')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options(collect(OperationStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options(collect(OperationType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()])),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\LotsRelationManager::class,
            RelationManagers\StagesRelationManager::class,
            RelationManagers\TransactionsRelationManager::class,
            RelationManagers\DocumentsRelationManager::class,
            RelationManagers\EventsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOperations::route('/'),
            'create' => Pages\CreateOperation::route('/create'),
            'edit' => Pages\EditOperation::route('/{record}/edit'),
            'view' => Pages\ViewOperation::route('/{record}'),
        ];
    }
}
