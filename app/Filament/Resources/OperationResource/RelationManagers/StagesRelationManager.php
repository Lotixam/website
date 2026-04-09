<?php

namespace App\Filament\Resources\OperationResource\RelationManagers;

use App\Enums\StageStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class StagesRelationManager extends RelationManager
{
    protected static string $relationship = 'stages';

    protected static ?string $title = 'Pipeline / Étapes';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('status')
                    ->label('Statut')
                    ->options(collect(StageStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                    ->default('pending')
                    ->required(),
                DateTimePicker::make('completed_at')
                    ->label('Terminé le'),
                Textarea::make('notes')
                    ->label('Notes')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ColorColumn::make('color')
                    ->label(''),
                Tables\Columns\TextColumn::make('name')
                    ->label('Étape')
                    ->sortable(),
                Tables\Columns\TextColumn::make('order')
                    ->label('Ordre')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pivot.status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => StageStatus::from($state)->label())
                    ->color(fn (string $state) => StageStatus::from($state)->color()),
                Tables\Columns\TextColumn::make('pivot.completed_at')
                    ->label('Terminé le')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->defaultSort('order')
            ->headerActions([
                Actions\AttachAction::make()
                    ->label('Ajouter une étape')
                    ->preloadRecordSelect()
                    ->form(fn (Actions\AttachAction $action) => [
                        $action->getRecordSelect(),
                        Select::make('status')
                            ->label('Statut')
                            ->options(collect(StageStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                            ->default('pending')
                            ->required(),
                        Textarea::make('notes')
                            ->label('Notes'),
                    ]),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
