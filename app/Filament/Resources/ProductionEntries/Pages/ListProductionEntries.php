<?php

namespace App\Filament\Resources\ProductionEntries\Pages;

use App\Filament\Resources\ProductionEntries\ProductionEntryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductionEntries extends ListRecords
{
    protected static string $resource = ProductionEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
