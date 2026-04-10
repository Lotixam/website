<?php

namespace App\Filament\Resources\WorkflowTemplateResource\RelationManagers;

use App\Enums\WorkflowParticipantVisibility;
use App\Enums\WorkflowValidationPolicy;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class NodesRelationManager extends RelationManager
{
    protected static string $relationship = 'nodes';

    protected static ?string $title = 'Étapes de ce type d\'opération';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('parent_id')
                    ->label('Étape parente (branche)')
                    ->placeholder('Racine : étape 1 du parcours')
                    ->options(function () {
                        return $this->getOwnerRecord()
                            ->nodes()
                            ->orderBy('sort_order')
                            ->pluck('title', 'id');
                    })
                    ->searchable(),
                TextInput::make('title')
                    ->label('Titre')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label('Ordre')
                    ->numeric()
                    ->default(0),
                TextInput::make('parallel_group')
                    ->label('Groupe parallèle')
                    ->maxLength(255)
                    ->helperText('Optionnel : nœuds avec le même groupe démarrent en parallèle.'),
                Toggle::make('is_merge_node')
                    ->label('Nœud de fusion')
                    ->default(false),
                Select::make('validation_policy')
                    ->label('Politique de validation')
                    ->options(collect(WorkflowValidationPolicy::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()]))
                    ->default(WorkflowValidationPolicy::LotixamOnly->value)
                    ->required(),
                Select::make('participant_visibility')
                    ->label('Visibilité')
                    ->options(collect(WorkflowParticipantVisibility::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()]))
                    ->default(WorkflowParticipantVisibility::AllAssigned->value)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')->label('Ordre')->sortable(),
                Tables\Columns\TextColumn::make('title')->label('Nom de l\'étape')->searchable(),
                Tables\Columns\TextColumn::make('parent.title')->label('Parent')->placeholder('—'),
                Tables\Columns\IconColumn::make('is_merge_node')->label('Fusion')->boolean(),
            ])
            ->headerActions([
                Actions\CreateAction::make()->label('Ajouter une étape'),
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
