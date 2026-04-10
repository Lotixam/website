<?php

namespace App\Filament\Resources\PublicMetricResource\Pages;

use App\Filament\Resources\PublicMetricResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPublicMetric extends EditRecord
{
    protected static string $resource = PublicMetricResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
