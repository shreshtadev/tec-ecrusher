<?php

namespace App\Filament\Resources\Challans\Pages;

use App\Events\ChallanCreated;
use App\Filament\Resources\Challans\ChallanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateChallan extends CreateRecord
{
    protected static string $resource = ChallanResource::class;

    protected function afterCreate(): void
    {
        ChallanCreated::dispatch($this->record);
    }
}
