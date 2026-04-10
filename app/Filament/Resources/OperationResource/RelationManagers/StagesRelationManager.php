<?php

namespace App\Filament\Resources\OperationResource\RelationManagers;

use App\Enums\StageSource;
use App\Enums\StageStatus;
use App\Models\DocumentRequest;
use App\Models\Stage;
use Filament\Actions;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class StagesRelationManager extends RelationManager
{
    protected static string $relationship = 'stages';

    protected static ?string $title = 'Pipeline / Étapes';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('status')
                    ->label('Statut')
                    ->options(collect(StageStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                    ->default('pending')
                    ->required(),
                DateTimePicker::make('completed_at')
                    ->label('Terminé le'),
                Textarea::make('notes')
                    ->label('Notes')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ColorColumn::make('source_color')
                    ->label('')
                    ->getStateUsing(function ($record) {
                        $source = $record->pivot->source ?? 'default';

                        return StageSource::tryFrom($source)?->color() ?? '#b1e90e';
                    }),
                Tables\Columns\TextColumn::make('name')
                    ->label('Étape')
                    ->sortable(),
                Tables\Columns\TextColumn::make('order')
                    ->label('Ordre')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pivot.source')
                    ->label('Ajouté par')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => StageSource::tryFrom($state ?? 'default')?->label() ?? 'Par défaut')
                    ->color(fn (?string $state) => match ($state) {
                        'collaborator' => 'info',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('pivot.status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => StageStatus::from($state)->label())
                    ->color(fn (string $state) => StageStatus::from($state)->color()),
                Tables\Columns\TextColumn::make('pivot.completed_at')
                    ->label('Terminé le')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->defaultSort('order')
            ->headerActions([
                Actions\AttachAction::make()
                    ->label('Ajouter une étape existante')
                    ->preloadRecordSelect()
                    ->form(fn (Actions\AttachAction $action) => [
                        $action->getRecordSelect(),
                        Select::make('status')
                            ->label('Statut')
                            ->options(collect(StageStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                            ->default('pending')
                            ->required(),
                        Textarea::make('notes')
                            ->label('Notes'),
                    ])
                    ->after(function (array $data) {
                        $source = auth()->user()->hasRole('collaborator') ? 'collaborator' : 'admin';
                        $operation = $this->getOwnerRecord();

                        $operation->stages()
                            ->wherePivot('stage_id', $data['recordId'])
                            ->updateExistingPivot($data['recordId'], [
                                'added_by_user_id' => auth()->id(),
                                'source' => $source,
                            ]);

                        $this->generateDocumentRequests($data['recordId'], $operation->id);
                    }),

                Actions\Action::make('createCustomStage')
                    ->label('Créer une étape personnalisée')
                    ->icon('heroicon-o-plus-circle')
                    ->form([
                        TextInput::make('name')
                            ->label('Nom de l\'étape')
                            ->required(),
                        Textarea::make('description')
                            ->label('Description'),
                        ColorPicker::make('color')
                            ->label('Couleur')
                            ->default('#b1e90e'),
                    ])
                    ->action(function (array $data) {
                        $maxOrder = $this->getOwnerRecord()
                            ->stages()
                            ->max('stages.order') ?? 0;

                        $stage = Stage::create([
                            'name' => $data['name'],
                            'description' => $data['description'] ?? null,
                            'color' => $data['color'],
                            'order' => $maxOrder + 1,
                            'is_default' => false,
                        ]);

                        $source = auth()->user()->hasRole('collaborator') ? 'collaborator' : 'admin';

                        $this->getOwnerRecord()->stages()->attach($stage->id, [
                            'status' => 'pending',
                            'added_by_user_id' => auth()->id(),
                            'source' => $source,
                        ]);
                    }),
            ])
            ->actions([
                Actions\Action::make('complete')
                    ->label('Terminer')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->hidden(fn ($record) => $record->pivot->status === 'completed')
                    ->action(function ($record) {
                        $this->getOwnerRecord()->stages()->updateExistingPivot($record->id, [
                            'status' => 'completed',
                            'completed_at' => now(),
                        ]);
                    }),

                Actions\Action::make('skip')
                    ->label('Passer')
                    ->icon('heroicon-o-forward')
                    ->color('info')
                    ->requiresConfirmation()
                    ->hidden(fn ($record) => in_array($record->pivot->status, ['completed', 'skipped']))
                    ->action(function ($record) {
                        $this->getOwnerRecord()->stages()->updateExistingPivot($record->id, [
                            'status' => 'skipped',
                        ]);
                    }),

                Actions\Action::make('reset')
                    ->label('Remettre en attente')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->hidden(fn ($record) => $record->pivot->status === 'pending')
                    ->action(function ($record) {
                        $this->getOwnerRecord()->stages()->updateExistingPivot($record->id, [
                            'status' => 'pending',
                            'completed_at' => null,
                        ]);
                    }),

                Actions\EditAction::make(),
                Actions\DetachAction::make()->label('Retirer'),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DetachBulkAction::make(),
                ]),
            ]);
    }

    protected function generateDocumentRequests(int $stageId, int $operationId): void
    {
        $templates = Stage::find($stageId)?->documentTemplates ?? collect();
        $operation = $this->getOwnerRecord();

        $clientUser = $operation->assignedUsers()
            ->wherePivot('role', 'client')
            ->first();

        foreach ($templates as $template) {
            DocumentRequest::create([
                'operation_id' => $operationId,
                'stage_id' => $stageId,
                'requested_by_user_id' => auth()->id(),
                'assigned_to_user_id' => $clientUser?->id,
                'name' => $template->name,
                'description' => $template->description,
                'status' => 'pending',
            ]);
        }
    }
}
