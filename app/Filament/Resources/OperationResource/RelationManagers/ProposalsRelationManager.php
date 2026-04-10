<?php

namespace App\Filament\Resources\OperationResource\RelationManagers;

use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ProposalsRelationManager extends RelationManager
{
    protected static string $relationship = 'proposals';

    protected static ?string $title = 'Propositions (prix)';

    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('amount')
                    ->label('Montant proposé')
                    ->numeric()
                    ->required()
                    ->prefix('€'),
                Textarea::make('notes')->label('Notes')->columnSpanFull(),
                Select::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'accepted' => 'Acceptée',
                        'rejected' => 'Refusée',
                    ])
                    ->default('pending')
                    ->required(),
                Select::make('operation_workflow_node_id')
                    ->label('Étape workflow liée')
                    ->options(fn () => $this->getOwnerRecord()->workflowNodes()->pluck('title', 'id'))
                    ->searchable()
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('amount')->label('Montant')->money('EUR'),
                Tables\Columns\TextColumn::make('status')->label('Statut')->badge(),
                Tables\Columns\TextColumn::make('workflowNode.title')->label('Étape')->placeholder('—'),
                Tables\Columns\TextColumn::make('created_at')->label('Date')->dateTime('d/m/Y'),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Actions\CreateAction::make()->label('Ajouter une proposition'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ]);
    }
}
