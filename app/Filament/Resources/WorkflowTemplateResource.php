<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkflowTemplateResource\Pages;
use App\Filament\Resources\WorkflowTemplateResource\RelationManagers;
use App\Models\WorkflowTemplate;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class WorkflowTemplateResource extends Resource
{
    protected static ?string $model = WorkflowTemplate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $slug = 'types-d-operations';

    protected static string|\UnitEnum|null $navigationGroup = 'Templates d\'opérations';

    /** Liste des types (terrain, immeuble, B2B…) — chaque fiche contient les étapes du parcours. */
    protected static ?string $navigationLabel = 'Types d\'opération';

    protected static ?string $modelLabel = 'Type d\'opération';

    protected static ?string $pluralModelLabel = 'Types d\'opération';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('key')
                    ->label('Clé technique')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Identifiant stable (snake_case), utilisé en base et dans les imports.'),
                TextInput::make('name')
                    ->label('Nom du type d\'opération')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Ex. « Division parcellaire », « Immeuble de rapport », « Partenaire financeur » — un type = un parcours. Les étapes se gèrent dans l’onglet en bas de la fiche.'),
                Textarea::make('description')
                    ->label('Description du parcours')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Actif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->label('Clé')->searchable(),
                Tables\Columns\TextColumn::make('name')->label('Type d\'opération')->searchable(),
                Tables\Columns\IconColumn::make('is_active')->label('Actif')->boolean(),
                Tables\Columns\TextColumn::make('nodes_count')->label('Étapes')->counts('nodes'),
            ])
            ->actions([Actions\EditAction::make(), Actions\DeleteAction::make()])
            ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\NodesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkflowTemplates::route('/'),
            'create' => Pages\CreateWorkflowTemplate::route('/create'),
            'edit' => Pages\EditWorkflowTemplate::route('/{record}/edit'),
        ];
    }
}
