<?php

namespace App\Filament\Resources;

use App\Enums\EventType;
use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Actions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|\UnitEnum|null $navigationGroup = 'Commercial';

    protected static ?string $modelLabel = 'Événement';

    protected static ?string $pluralModelLabel = 'Agenda';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')->label('Titre')->required()->columnSpanFull(),
                        Select::make('type')->label('Type')
                            ->options(collect(EventType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()]))
                            ->default('reminder')->required(),
                        Toggle::make('is_completed')->label('Terminé'),
                        DateTimePicker::make('start_at')->label('Début')->required(),
                        DateTimePicker::make('end_at')->label('Fin'),
                        Select::make('operation_id')->label('Opération')->relationship('operation', 'name')->searchable()->preload(),
                        Select::make('lot_id')->label('Unité du bien')->relationship('lot', 'lot_number')->searchable()->preload(),
                        Select::make('contact_id')->label('Contact')->relationship('contact', 'last_name')
                            ->getOptionLabelFromRecordUsing(fn ($r) => "{$r->first_name} {$r->last_name}")
                            ->searchable(['first_name', 'last_name'])->preload(),
                        RichEditor::make('description')->label('Description')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Titre')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->label('Type')->badge()
                    ->formatStateUsing(fn (EventType $state) => $state->label())
                    ->color(fn (EventType $state) => $state->color()),
                Tables\Columns\TextColumn::make('start_at')->label('Date')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('operation.name')->label('Opération')->toggleable(),
                Tables\Columns\IconColumn::make('is_completed')->label('Fait')->boolean(),
            ])
            ->defaultSort('start_at', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')->label('Type')
                    ->options(collect(EventType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()])),
                Tables\Filters\SelectFilter::make('operation_id')->label('Opération')->relationship('operation', 'name'),
                Tables\Filters\TernaryFilter::make('is_completed')->label('Terminé'),
            ])
            ->actions([Actions\EditAction::make(), Actions\DeleteAction::make()])
            ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
