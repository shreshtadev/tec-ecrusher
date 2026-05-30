<?php

namespace App\Filament\Resources\ProductionEntries\Pages;

use App\Filament\Resources\Items\ItemResource;
use App\Filament\Resources\ProductionEntries\ProductionEntryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProductionEntry extends CreateRecord
{
    protected static string $resource = ProductionEntryResource::class;

    protected function getRedirectUrl(): string
    {
        return ItemResource::getUrl('index');
    }
}
