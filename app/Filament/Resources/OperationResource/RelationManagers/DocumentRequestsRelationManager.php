<?php

namespace App\Filament\Resources\OperationResource\RelationManagers;

use App\Enums\DocumentRequestStatus;
use App\Enums\WorkflowReopenScope;
use App\Models\User;
use App\Services\Workflow\WorkflowEngine;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class DocumentRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'documentRequests';

    protected static ?string $title = 'Demandes de documents';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->label('Document demandé')
                    ->required(),
                Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull(),
                Select::make('stage_id')
                    ->label('Étape catalogue (legacy)')
                    ->options(fn () => $this->getOwnerRecord()->stages->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                Select::make('operation_workflow_node_id')
                    ->label('Étape workflow')
                    ->options(fn () => $this->getOwnerRecord()->workflowNodes()->pluck('title', 'id'))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('assigned_to_user_id')
                    ->label('Assigné au client')
                    ->options(fn () => $this->getOwnerRecord()
                        ->assignedUsers()
                        ->wherePivot('role', 'client')
                        ->get()
                        ->mapWithKeys(fn (User $u) => [$u->id => $u->name])
                    )
                    ->searchable()
                    ->preload(),
                DatePicker::make('due_date')
                    ->label('Date limite'),
                Select::make('status')
                    ->label('Statut')
                    ->options(collect(DocumentRequestStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                    ->default('pending')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Document')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stage.name')
                    ->label('Étape catalogue')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('workflowNode.title')
                    ->label('Étape workflow')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Client')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (DocumentRequestStatus $state) => $state->label())
                    ->color(fn (DocumentRequestStatus $state) => $state->color()),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Échéance')
                    ->date('d/m/Y')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('requestedBy.name')
                    ->label('Demandé par')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Demander un document')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['requested_by_user_id'] = auth()->id();

                        return $data;
                    }),
            ])
            ->actions([
                Actions\Action::make('approve')
                    ->label('Valider')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->hidden(fn ($record) => $record->status !== DocumentRequestStatus::Uploaded)
                    ->action(fn ($record) => $record->update([
                        'status' => 'approved',
                        'reviewed_at' => now(),
                        'reviewed_by_user_id' => auth()->id(),
                    ])),

                Actions\Action::make('reject')
                    ->label('Refuser')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->hidden(fn ($record) => $record->status !== DocumentRequestStatus::Uploaded)
                    ->action(fn ($record) => $record->update([
                        'status' => 'rejected',
                        'reviewed_at' => now(),
                        'reviewed_by_user_id' => auth()->id(),
                    ])),
                Actions\Action::make('rejectSingleDocumentRevision')
                    ->label('Refuser + révision ciblée')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->hidden(fn ($record) => $record->status !== DocumentRequestStatus::Uploaded || ! $record->operation_workflow_node_id)
                    ->action(function ($record): void {
                        $record->update([
                            'status' => 'rejected',
                            'reviewed_at' => now(),
                            'reviewed_by_user_id' => auth()->id(),
                        ]);

                        app(WorkflowEngine::class)->rejectAndReopen(
                            auth()->user(),
                            $record->workflowNode,
                            WorkflowReopenScope::SingleDocument,
                            $record->id,
                            'Révision ciblée depuis la demande de document.',
                        );
                    }),

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
