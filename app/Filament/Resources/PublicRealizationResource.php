<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PublicRealizationResource\Pages;
use App\Filament\Resources\PublicRealizationResource\RelationManagers;
use App\Models\PublicRealization;
use Filament\Actions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PublicRealizationResource extends Resource
{
    protected static ?string $model = PublicRealization::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static string|\UnitEnum|null $navigationGroup = 'Vitrine';

    protected static ?string $navigationLabel = 'Nos réalisations';

    protected static ?string $modelLabel = 'Réalisation';

    protected static ?string $pluralModelLabel = 'Nos réalisations';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Titre')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('excerpt')
                            ->label('Accroche / extrait')
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('sort_order')
                            ->label('Ordre')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1),
                        Toggle::make('is_visible')
                            ->label('Visible sur le site')
                            ->default(true)
                            ->required(),
                    ]),
                Section::make()
                    ->schema([
                        RichEditor::make('body')
                            ->label('Texte principal')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                Section::make('Chiffres clés (grille type indicateurs)')
                    ->schema([
                        Repeater::make('highlights')
                            ->label('Lignes')
                            ->schema([
                                TextInput::make('label')
                                    ->label('Libellé')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('value')
                                    ->label('Valeur affichée')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->columns(2)
                            ->default([])
                            ->addActionLabel('Ajouter une ligne')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slides_count')
                    ->label('Photos')
                    ->counts('slides'),
                Tables\Columns\IconColumn::make('is_visible')
                    ->label('Visible')
                    ->boolean(),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\SlidesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPublicRealizations::route('/'),
            'create' => Pages\CreatePublicRealization::route('/create'),
            'edit' => Pages\EditPublicRealization::route('/{record}/edit'),
        ];
    }
}
