<?php

namespace App\Filament\Resources\OperationResource\RelationManagers;

use App\Enums\EventType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class EventsRelationManager extends RelationManager
{
    protected static string $relationship = 'events';

    protected static ?string $title = 'Agenda';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('title')
                    ->label('Titre')
                    ->required(),
                Select::make('type')
                    ->label('Type')
                    ->options(collect(EventType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()]))
                    ->default('reminder')
                    ->required(),
                DateTimePicker::make('start_at')
                    ->label('Début')
                    ->required(),
                DateTimePicker::make('end_at')
                    ->label('Fin'),
                Toggle::make('is_completed')
                    ->label('Terminé'),
                Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (EventType $state) => $state->label())
                    ->color(fn (EventType $state) => $state->color()),
                Tables\Columns\TextColumn::make('start_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_completed')
                    ->label('Fait')
                    ->boolean(),
            ])
            ->defaultSort('start_at', 'asc')
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ]);
    }
}
