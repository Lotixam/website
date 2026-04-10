<?php

namespace App\Filament\Resources;

use App\Enums\LotStatus;
use App\Filament\Resources\LotResource\Pages;
use App\Models\Contact;
use App\Models\Lot;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LotResource extends Resource
{
    protected static ?string $model = Lot::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string|\UnitEnum|null $navigationGroup = 'Opérations';

    protected static ?string $modelLabel = 'Unité du bien';

    protected static ?string $pluralModelLabel = 'Unités du bien';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informations de l\'unité')
                    ->columns(2)
                    ->schema([
                        Select::make('operation_id')
                            ->label('Opération')
                            ->relationship('operation', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Optionnel : laisser vide pour un bien déjà présent avant la structuration en opération, ou pour constituer un historique manuel (documents, notes sur l’unité).'),
                        TextInput::make('lot_number')
                            ->label('Référence / n° (lot, parcelle, lotissement…)')
                            ->required(),
                        TextInput::make('surface')
                            ->label('Surface (m²)')
                            ->numeric()
                            ->suffix('m²'),
                        TextInput::make('selling_price')
                            ->label('Prix de vente')
                            ->numeric()
                            ->prefix('€'),
                        Select::make('status')
                            ->label('Statut')
                            ->options(collect(LotStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                            ->default('available')
                            ->required(),
                        Select::make('buyer_contact_id')
                            ->label('Acquéreur')
                            ->relationship('buyer', 'last_name')
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
                                $data['type'] = 'buyer';

                                return Contact::create($data)->id;
                            }),
                        DatePicker::make('sold_at')
                            ->label('Date de vente'),
                    ]),
                Section::make('Détails')
                    ->schema([
                        Textarea::make('description')->label('Description'),
                        RichEditor::make('notes')->label('Notes'),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lot_number')->label('Réf.')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('operation.name')
                    ->label('Opération')
                    ->placeholder('—')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('surface')->label('Surface')->suffix(' m²')->sortable(),
                Tables\Columns\TextColumn::make('selling_price')->label('Prix')->money('EUR')->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Statut')->badge()
                    ->formatStateUsing(fn (LotStatus $state) => $state->label())
                    ->color(fn (LotStatus $state) => $state->color()),
                Tables\Columns\TextColumn::make('buyer.last_name')->label('Acquéreur')
                    ->formatStateUsing(fn ($record) => $record->buyer ? "{$record->buyer->first_name} {$record->buyer->last_name}" : '—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('has_operation')
                    ->label('Rattachement opération')
                    ->placeholder('Toutes les unités')
                    ->trueLabel('Avec opération')
                    ->falseLabel('Sans opération')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('operation_id'),
                        false: fn (Builder $query) => $query->whereNull('operation_id'),
                        blank: fn (Builder $query) => $query,
                    ),
                Tables\Filters\SelectFilter::make('operation_id')->label('Opération')->relationship('operation', 'name'),
            ])
            ->actions([Actions\EditAction::make()])
            ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLots::route('/'),
            'create' => Pages\CreateLot::route('/create'),
            'edit' => Pages\EditLot::route('/{record}/edit'),
        ];
    }
}
