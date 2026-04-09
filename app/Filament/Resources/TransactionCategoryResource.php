<?php

namespace App\Filament\Resources;

use App\Enums\TransactionType;
use App\Filament\Resources\TransactionCategoryResource\Pages;
use App\Models\TransactionCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class TransactionCategoryResource extends Resource
{
    protected static ?string $model = TransactionCategory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|\UnitEnum|null $navigationGroup = 'Finances';

    protected static ?string $modelLabel = 'Catégorie';

    protected static ?string $pluralModelLabel = 'Catégories de transactions';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')->label('Nom')->required(),
                Select::make('type')->label('Type')
                    ->options(collect(TransactionType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()]))->required(),
                Toggle::make('is_default')->label('Par défaut'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Catégorie')->searchable(),
                Tables\Columns\TextColumn::make('type')->label('Type')->badge()
                    ->formatStateUsing(fn (TransactionType $state) => $state->label())
                    ->color(fn (TransactionType $state) => $state->color()),
                Tables\Columns\IconColumn::make('is_default')->label('Par défaut')->boolean(),
                Tables\Columns\TextColumn::make('transactions_count')->label('Transactions')->counts('transactions'),
            ])
            ->actions([Actions\EditAction::make(), Actions\DeleteAction::make()])
            ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactionCategories::route('/'),
            'create' => Pages\CreateTransactionCategory::route('/create'),
            'edit' => Pages\EditTransactionCategory::route('/{record}/edit'),
        ];
    }
}
