<?php

namespace App\Filament\Resources\PublicRealizationResource\RelationManagers;

use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SlidesRelationManager extends RelationManager
{
    protected static string $relationship = 'slides';

    protected static ?string $title = 'Photos du carrousel';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FileUpload::make('image_path')
                    ->label('Image')
                    ->image()
                    ->required()
                    ->directory('realizations/slides')
                    ->disk('public')
                    ->columnSpanFull(),
                TextInput::make('caption')
                    ->label('Légende (alt / sous-titre)')
                    ->maxLength(255),
                TextInput::make('sort_order')
                    ->label('Ordre')
                    ->numeric()
                    ->required()
                    ->default(1)
                    ->minValue(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('')
                    ->disk('public')
                    ->height(48),
                Tables\Columns\TextColumn::make('caption')
                    ->label('Légende')
                    ->limit(40),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordre')
                    ->sortable(),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Ajouter une photo')
                    ->mutateFormDataUsing(fn (array $data): array => array_merge($data, [
                        'public_realization_id' => $this->getOwnerRecord()->getKey(),
                    ])),
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
