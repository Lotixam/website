<?php

namespace App\Filament\Resources\PublicRealizationResource\Pages;

use App\Filament\Resources\PublicRealizationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPublicRealizations extends ListRecords
{
    protected static string $resource = PublicRealizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
