<?php

namespace App\Filament\Resources\OperationResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class WorkflowAuditEventsRelationManager extends RelationManager
{
    protected static string $relationship = 'workflowAuditEvents';

    protected static ?string $title = 'Historique workflow';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('event_type')
                    ->label('Événement')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->placeholder('Système'),
                Tables\Columns\TextColumn::make('payload')
                    ->label('Détails')
                    ->formatStateUsing(fn (?array $state) => $state ? json_encode($state, JSON_UNESCAPED_UNICODE) : '—')
                    ->limit(80)
                    ->tooltip(fn ($record) => $record->payload ? json_encode($record->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : null),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100]);
    }

    protected function canCreate(): bool
    {
        return false;
    }

    protected function canEdit($record): bool
    {
        return false;
    }

    protected function canDelete($record): bool
    {
        return false;
    }

    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }
}
