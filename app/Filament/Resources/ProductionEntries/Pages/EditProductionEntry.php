<?php

namespace App\Filament\Resources\ProductionEntries\Pages;

use App\Filament\Resources\ProductionEntries\ProductionEntryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditProductionEntry extends EditRecord
{
    protected static string $resource = ProductionEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
