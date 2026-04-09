<?php

namespace App\Filament\Widgets;

use App\Enums\EventType;
use App\Models\Event;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingEventsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected static ?string $heading = 'Prochains événements';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Event::query()
                    ->where('is_completed', false)
                    ->where('start_at', '>=', now())
                    ->orderBy('start_at')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (EventType $state) => $state->label())
                    ->color(fn (EventType $state) => $state->color()),
                Tables\Columns\TextColumn::make('start_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->paginated(false);
    }
}
