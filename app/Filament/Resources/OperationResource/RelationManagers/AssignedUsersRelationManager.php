<?php

namespace App\Filament\Resources\OperationResource\RelationManagers;

use App\Enums\OperationParticipantKind;
use App\Enums\ProjectRole;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AssignedUsersRelationManager extends RelationManager
{
    protected static string $relationship = 'assignedUsers';

    protected static ?string $title = 'Membres';

    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('role')
                    ->label('Rôle sur ce projet')
                    ->options(collect(ProjectRole::cases())->mapWithKeys(fn ($r) => [$r->value => $r->label()]))
                    ->default('client')
                    ->required(),
                Select::make('participant_kind')
                    ->label('Type de participant')
                    ->options(collect(OperationParticipantKind::cases())->mapWithKeys(fn ($k) => [$k->value => $k->label()]))
                    ->nullable()
                    ->helperText('Ex. partenaire B2B pour masquer certaines étapes.'),
                Select::make('workflow_entry_node_id')
                    ->label('Point d\'entrée (timeline)')
                    ->options(fn () => $this->getOwnerRecord()->workflowNodes()->pluck('title', 'id'))
                    ->searchable()
                    ->nullable()
                    ->helperText('Le participant ne voit que cette étape et les suivantes (si masquage amont activé).'),
                Toggle::make('hide_upstream_steps')
                    ->label('Masquer les étapes avant le point d\'entrée')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email'),
                Tables\Columns\TextColumn::make('pivot.role')
                    ->label('Rôle projet')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ProjectRole::from($state)->label())
                    ->color(fn (string $state) => ProjectRole::from($state)->color()),
                Tables\Columns\TextColumn::make('pivot.assigned_at')
                    ->label('Assigné le')
                    ->dateTime('d/m/Y'),
                Tables\Columns\TextColumn::make('pivot.participant_kind')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state ? OperationParticipantKind::from($state)->label() : '—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Actions\AttachAction::make()
                    ->label('Ajouter un membre')
                    ->preloadRecordSelect()
                    ->form(fn (Actions\AttachAction $action) => [
                        $action->getRecordSelect(),
                        Select::make('role')
                            ->label('Rôle sur ce projet')
                            ->options(collect(ProjectRole::cases())->mapWithKeys(fn ($r) => [$r->value => $r->label()]))
                            ->default('client')
                            ->required(),
                        Select::make('participant_kind')
                            ->label('Type de participant')
                            ->options(collect(OperationParticipantKind::cases())->mapWithKeys(fn ($k) => [$k->value => $k->label()]))
                            ->nullable(),
                        Select::make('workflow_entry_node_id')
                            ->label('Point d\'entrée workflow')
                            ->options(fn () => $this->getOwnerRecord()->workflowNodes()->pluck('title', 'id'))
                            ->searchable()
                            ->nullable(),
                        Toggle::make('hide_upstream_steps')
                            ->label('Masquer étapes amont')
                            ->default(true),
                    ]),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
