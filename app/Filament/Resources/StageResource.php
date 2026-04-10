<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StageResource\Pages;
use App\Models\Stage;
use Filament\Actions;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class StageResource extends Resource
{
    protected static ?string $model = Stage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'Étapes catalogue (legacy)';

    protected static ?string $modelLabel = 'Étape du catalogue';

    protected static ?string $pluralModelLabel = 'Étapes du catalogue linéaire';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')->label('Nom de l\'étape')->required(),
                Textarea::make('description')->label('Description'),
                ColorPicker::make('color')->label('Couleur')->default('#7c3aed'),
                TextInput::make('order')->label('Ordre')->numeric()->default(0),
                Toggle::make('is_default')->label('Inclure par défaut sur les nouvelles opérations')
                    ->helperText('Ancien pipeline linéaire partagé entre toutes les opérations. Les parcours par type d’opération (étapes 1, 2, 3…) se définissent dans Administration → Templates d’opérations → Types d’opération.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order')
            ->columns([
                Tables\Columns\ColorColumn::make('color')->label(''),
                Tables\Columns\TextColumn::make('name')->label('Nom')->searchable(),
                Tables\Columns\TextColumn::make('order')->label('Ordre')->sortable(),
                Tables\Columns\IconColumn::make('is_default')->label('Par défaut')->boolean(),
                Tables\Columns\TextColumn::make('operations_count')->label('Opérations')->counts('operations'),
            ])
            ->actions([Actions\EditAction::make(), Actions\DeleteAction::make()])
            ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [
            StageResource\RelationManagers\DocumentTemplatesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStages::route('/'),
            'create' => Pages\CreateStage::route('/create'),
            'edit' => Pages\EditStage::route('/{record}/edit'),
        ];
    }
}
