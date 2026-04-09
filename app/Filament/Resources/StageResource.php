<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StageResource\Pages;
use App\Models\Stage;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class StageResource extends Resource
{
    protected static ?string $model = Stage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    protected static ?string $modelLabel = 'Étape';

    protected static ?string $pluralModelLabel = 'Étapes';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')->label('Nom de l\'étape')->required(),
                Textarea::make('description')->label('Description'),
                ColorPicker::make('color')->label('Couleur')->default('#7c3aed'),
                TextInput::make('order')->label('Ordre')->numeric()->default(0),
                Toggle::make('is_default')->label('Étape par défaut')
                    ->helperText('Les étapes par défaut sont automatiquement proposées pour les nouvelles opérations'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order')
            ->columns([
                Tables\Columns\ColorColumn::make('color')->label(''),
                Tables\Columns\TextColumn::make('name')->label('Étape')->searchable(),
                Tables\Columns\TextColumn::make('order')->label('Ordre')->sortable(),
                Tables\Columns\IconColumn::make('is_default')->label('Par défaut')->boolean(),
                Tables\Columns\TextColumn::make('operations_count')->label('Opérations')->counts('operations'),
            ])
            ->actions([Actions\EditAction::make(), Actions\DeleteAction::make()])
            ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
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
