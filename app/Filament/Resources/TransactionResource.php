<?php

namespace App\Filament\Resources;

use App\Enums\TransactionType;
use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Finances';

    protected static ?string $modelLabel = 'Transaction';

    protected static ?string $pluralModelLabel = 'Transactions';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        Select::make('operation_id')->label('Opération')->relationship('operation', 'name')->searchable()->preload()->required(),
                        Select::make('lot_id')->label('Lot')->relationship('lot', 'lot_number')->searchable()->preload(),
                        Select::make('type')->label('Type')
                            ->options(collect(TransactionType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()]))
                            ->required()->reactive(),
                        Select::make('category_id')->label('Catégorie')
                            ->options(fn (Get $get) => TransactionCategory::where('type', $get('type') ?? 'expense')->pluck('name', 'id'))
                            ->preload()->searchable(),
                        TextInput::make('amount')->label('Montant')->numeric()->prefix('€')->required(),
                        DatePicker::make('date')->label('Date')->default(now())->required(),
                        Select::make('contact_id')->label('Contact')->relationship('contact', 'last_name')
                            ->getOptionLabelFromRecordUsing(fn ($r) => "{$r->first_name} {$r->last_name}")
                            ->searchable(['first_name', 'last_name'])->preload(),
                        TextInput::make('description')->label('Description'),
                        Textarea::make('notes')->label('Notes')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')->label('Date')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('operation.name')->label('Opération')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->label('Type')->badge()
                    ->formatStateUsing(fn (TransactionType $state) => $state->label())
                    ->color(fn (TransactionType $state) => $state->color()),
                Tables\Columns\TextColumn::make('category.name')->label('Catégorie')->sortable(),
                Tables\Columns\TextColumn::make('description')->label('Description')->limit(30)->searchable(),
                Tables\Columns\TextColumn::make('amount')->label('Montant')->money('EUR')->sortable(),
                Tables\Columns\TextColumn::make('lot.lot_number')->label('Lot')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('operation_id')->label('Opération')->relationship('operation', 'name'),
                Tables\Filters\SelectFilter::make('type')->label('Type')
                    ->options(collect(TransactionType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()])),
                Tables\Filters\SelectFilter::make('category_id')->label('Catégorie')->relationship('category', 'name'),
            ])
            ->actions([Actions\EditAction::make()])
            ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
