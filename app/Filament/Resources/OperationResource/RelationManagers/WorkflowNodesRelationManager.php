<?php

namespace App\Filament\Resources\OperationResource\RelationManagers;

use App\Enums\WorkflowNodeStatus;
use App\Enums\WorkflowParticipantVisibility;
use App\Enums\WorkflowReopenScope;
use App\Enums\WorkflowValidationPolicy;
use App\Models\DocumentRequest;
use App\Models\OperationWorkflowNode;
use App\Models\WorkflowTemplate;
use App\Services\Workflow\WorkflowEngine;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WorkflowNodesRelationManager extends RelationManager
{
    protected static string $relationship = 'workflowNodes';

    protected static ?string $title = 'Workflow (étapes & branches)';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $q) => $q->with(['parent', 'blockedBy', 'approvals']))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Étape')
                    ->formatStateUsing(function (string $state, OperationWorkflowNode $record) {
                        $depth = 0;
                        $p = $record->parent;
                        while ($p) {
                            $depth++;
                            $p = $p->parent;
                        }

                        return str_repeat('— ', $depth).$state;
                    })
                    ->description(fn (OperationWorkflowNode $record) => $record->is_merge_node ? 'Point de fusion' : null),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (WorkflowNodeStatus $state) => $state->label())
                    ->color(fn (WorkflowNodeStatus $state) => $state->color()),
                Tables\Columns\TextColumn::make('validation_policy')
                    ->label('Validation')
                    ->badge()
                    ->formatStateUsing(fn (WorkflowValidationPolicy $state) => $state->label())
                    ->toggleable(),
                Tables\Columns\TextColumn::make('participant_visibility')
                    ->label('Visibilité')
                    ->badge()
                    ->formatStateUsing(fn (WorkflowParticipantVisibility $state) => $state->label())
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('blockedBy.title')
                    ->label('Bloqué par')
                    ->placeholder('—')
                    ->color('danger'),
                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Terminé le')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->headerActions([
                Actions\Action::make('instantiate')
                    ->label('Instancier depuis un modèle')
                    ->icon('heroicon-o-play')
                    ->visible(fn () => auth()->user()->hasRole('admin'))
                    ->disabled(fn () => $this->getOwnerRecord()->workflowNodes()->exists())
                    ->form([
                        Select::make('workflow_template_id')
                            ->label('Template d\'opération')
                            ->options(fn () => WorkflowTemplate::query()->where('is_active', true)->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                    ])
                    ->action(function (array $data): void {
                        $template = WorkflowTemplate::findOrFail($data['workflow_template_id']);
                        try {
                            app(WorkflowEngine::class)->instantiateFromTemplate($this->getOwnerRecord(), $template);
                            Notification::make()->title('Workflow instancié')->success()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),
            ])
            ->actions([
                Actions\Action::make('approve')
                    ->label('Valider ma partie')
                    ->icon('heroicon-o-check')
                    ->visible(function (OperationWorkflowNode $record) {
                        if ($record->status !== WorkflowNodeStatus::InProgress) {
                            return false;
                        }

                        return app(WorkflowEngine::class)->userCanApprove(auth()->user(), $record);
                    })
                    ->form([
                        Textarea::make('comment')->label('Commentaire (optionnel)'),
                    ])
                    ->action(function (OperationWorkflowNode $record, array $data): void {
                        try {
                            app(WorkflowEngine::class)->submitApproval(auth()->user(), $record, true, $data['comment'] ?? null);
                            Notification::make()->title('Validation enregistrée')->success()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),
                Actions\Action::make('completeDirect')
                    ->label('Terminer (Lotixam)')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (OperationWorkflowNode $record) => auth()->user()->hasRole('admin')
                        && $record->status === WorkflowNodeStatus::InProgress
                        && $record->validation_policy === WorkflowValidationPolicy::LotixamOnly)
                    ->requiresConfirmation()
                    ->action(function (OperationWorkflowNode $record): void {
                        try {
                            app(WorkflowEngine::class)->completeNodeDirectly(auth()->user(), $record);
                            Notification::make()->title('Étape terminée')->success()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),
                Actions\Action::make('skip')
                    ->label('Passer')
                    ->icon('heroicon-o-forward')
                    ->color('gray')
                    ->visible(fn (OperationWorkflowNode $record) => auth()->user()->hasRole('admin')
                        && in_array($record->status, [WorkflowNodeStatus::Pending, WorkflowNodeStatus::InProgress, WorkflowNodeStatus::Blocked], true))
                    ->requiresConfirmation()
                    ->action(function (OperationWorkflowNode $record): void {
                        try {
                            app(WorkflowEngine::class)->skipNode(auth()->user(), $record);
                            Notification::make()->title('Étape passée')->success()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),
                Actions\Action::make('reopen')
                    ->label('Réouvrir…')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->visible(fn () => auth()->user()->hasRole('admin'))
                    ->form([
                        Select::make('scope')
                            ->label('Périmètre')
                            ->options(collect(WorkflowReopenScope::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                            ->required(),
                        Select::make('document_request_id')
                            ->label('Document ciblé (si révision d\'un seul document)')
                            ->options(fn (OperationWorkflowNode $record) => DocumentRequest::query()
                                ->where('operation_id', $record->operation_id)
                                ->where('operation_workflow_node_id', $record->id)
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),
                        Textarea::make('comment')->label('Motif'),
                    ])
                    ->action(function (OperationWorkflowNode $record, array $data): void {
                        try {
                            app(WorkflowEngine::class)->rejectAndReopen(
                                auth()->user(),
                                $record,
                                WorkflowReopenScope::from($data['scope']),
                                $data['document_request_id'] ?? null,
                                $data['comment'] ?? null,
                            );
                            Notification::make()->title('Réouverture effectuée')->success()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),
            ])
            ->paginated(false);
    }

    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        $u = auth()->user();

        return $u && ($u->hasRole('admin') || $u->hasRole('collaborator'));
    }
}
