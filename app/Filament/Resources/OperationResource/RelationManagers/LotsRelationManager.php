<?php

namespace App\Filament\Resources\OperationResource\RelationManagers;

use App\Enums\LotStatus;
use App\Models\Contact;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class LotsRelationManager extends RelationManager
{
    protected static string $relationship = 'lots';

    protected static ?string $title = 'Lots / Parcelles';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('lot_number')
                    ->label('N° de lot')
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
                    ->options(fn () => Contact::where('type', 'buyer')->get()->mapWithKeys(fn ($c) => [$c->id => "{$c->first_name} {$c->last_name}"]))
                    ->searchable()
                    ->preload(),
                DatePicker::make('sold_at')
                    ->label('Date de vente'),
                Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label('Notes')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lot_number')
                    ->label('N° Lot')
                    ->sortable(),
                Tables\Columns\TextColumn::make('surface')
                    ->label('Surface')
                    ->suffix(' m²')
                    ->sortable(),
                Tables\Columns\TextColumn::make('selling_price')
                    ->label('Prix')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (LotStatus $state) => $state->label())
                    ->color(fn (LotStatus $state) => $state->color()),
                Tables\Columns\TextColumn::make('buyer.last_name')
                    ->label('Acquéreur')
                    ->formatStateUsing(fn ($record) => $record->buyer ? "{$record->buyer->first_name} {$record->buyer->last_name}" : '—'),
            ])
            ->headerActions([
                Actions\CreateAction::make(),
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
}
