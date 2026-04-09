<?php

namespace App\Filament\Resources\OperationResource\RelationManagers;

use App\Enums\TransactionType;
use App\Models\TransactionCategory;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    protected static ?string $title = 'Finances';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('type')
                    ->label('Type')
                    ->options(collect(TransactionType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()]))
                    ->required()
                    ->reactive(),
                Select::make('category_id')
                    ->label('Catégorie')
                    ->options(fn (Get $get) => TransactionCategory::where('type', $get('type') ?? 'expense')->pluck('name', 'id'))
                    ->preload()
                    ->searchable(),
                TextInput::make('amount')
                    ->label('Montant')
                    ->numeric()
                    ->prefix('€')
                    ->required(),
                DatePicker::make('date')
                    ->label('Date')
                    ->default(now())
                    ->required(),
                TextInput::make('description')
                    ->label('Description')
                    ->columnSpanFull(),
                Select::make('lot_id')
                    ->label('Lot concerné')
                    ->relationship('lot', 'lot_number')
                    ->searchable()
                    ->preload(),
                Textarea::make('notes')
                    ->label('Notes')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (TransactionType $state) => $state->label())
                    ->color(fn (TransactionType $state) => $state->color()),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Catégorie'),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(40),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Montant')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('lot.lot_number')
                    ->label('Lot'),
            ])
            ->defaultSort('date', 'desc')
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
