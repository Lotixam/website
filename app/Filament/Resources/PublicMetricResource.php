<?php

namespace App\Filament\Resources;

use App\Enums\PublicMetricSource;
use App\Filament\Resources\PublicMetricResource\Pages;
use App\Models\PublicMetric;
use App\Services\PublicMetricValueResolver;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PublicMetricResource extends Resource
{
    protected static ?string $model = PublicMetric::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-eye';

    protected static string|\UnitEnum|null $navigationGroup = 'Vitrine';

    protected static ?string $navigationLabel = 'Indicateurs publics';

    protected static ?string $modelLabel = 'Indicateur public';

    protected static ?string $pluralModelLabel = 'Indicateurs publics';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('label')
                    ->label('Libellé affiché')
                    ->required()
                    ->maxLength(255),
                Select::make('source')
                    ->label('Source')
                    ->required()
                    ->options(collect(PublicMetricSource::cases())->mapWithKeys(
                        fn (PublicMetricSource $source): array => [$source->value => $source->label()]
                    )),
                TextInput::make('value_override')
                    ->label('Valeur forcée (optionnel)')
                    ->numeric()
                    ->minValue(0)
                    ->helperText('Si renseigné, cette valeur remplace le calcul automatique.'),
                TextInput::make('suffix')
                    ->label('Suffixe')
                    ->maxLength(20)
                    ->default('+')
                    ->helperText('Exemple : +'),
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Indicateur')
                    ->searchable(),
                Tables\Columns\TextColumn::make('source')
                    ->label('Source')
                    ->badge()
                    ->formatStateUsing(function (PublicMetricSource|string $state): string {
                        if ($state instanceof PublicMetricSource) {
                            return $state->label();
                        }

                        return PublicMetricSource::tryFrom((string) $state)?->label() ?? (string) $state;
                    }),
                Tables\Columns\TextColumn::make('resolved_value')
                    ->label('Valeur affichée')
                    ->state(function (PublicMetric $record): string {
                        $value = app(PublicMetricValueResolver::class)->resolve($record);
                        $suffix = is_string($record->suffix) ? $record->suffix : '';

                        return number_format($value, 0, ',', ' ').$suffix;
                    }),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPublicMetrics::route('/'),
            'create' => Pages\CreatePublicMetric::route('/create'),
            'edit' => Pages\EditPublicMetric::route('/{record}/edit'),
        ];
    }
}
