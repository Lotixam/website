<?php

namespace App\Filament\Resources\OperationResource\Pages;

use App\Filament\Resources\OperationResource;
use App\Services\Workflow\WorkflowEngine;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditOperation extends EditRecord
{
    protected static string $resource = OperationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('closeProject')
                ->label('Clôturer définitivement')
                ->icon('heroicon-o-lock-closed')
                ->color('warning')
                ->visible(fn () => auth()->user()->hasRole('admin') && ! $this->record->closed_at)
                ->requiresConfirmation()
                ->modalDescription('Seul Lotixam peut clôturer un projet une fois toutes les étapes terminées.')
                ->action(function (): void {
                    try {
                        app(WorkflowEngine::class)->closeOperationByLotixam(auth()->user(), $this->record);
                        $this->record->refresh();
                        Notification::make()->title('Projet clôturé')->success()->send();
                    } catch (\Throwable $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();
                    }
                }),
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
