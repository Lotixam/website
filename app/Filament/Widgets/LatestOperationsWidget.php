<?php

namespace App\Filament\Widgets;

use App\Enums\OperationStatus;
use App\Filament\Resources\OperationResource;
use App\Models\Operation;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOperationsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Dernières opérations';

    public function table(Table $table): Table
    {
        return $table
            ->query(Operation::query()->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Opération'),
                Tables\Columns\TextColumn::make('city')
                    ->label('Ville'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (?OperationStatus $state) => $state?->label() ?? '—')
                    ->color(fn (?OperationStatus $state) => $state?->color() ?? 'gray'),
                Tables\Columns\TextColumn::make('purchase_price')
                    ->label('Prix d\'achat')
                    ->money('EUR'),
                Tables\Columns\TextColumn::make('lots_count')
                    ->label('Unités')
                    ->counts('lots'),
            ])
            ->actions([
                Action::make('view')
                    ->label('Voir')
                    ->url(fn (Operation $record) => OperationResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-m-eye'),
            ])
            ->paginated(false);
    }
}
