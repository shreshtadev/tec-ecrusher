<?php

namespace App\Filament\Resources\Challans\Pages;

use App\Events\ChallanDeleted;
use App\Filament\Resources\Challans\ChallanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditChallan extends EditRecord
{
    protected static string $resource = ChallanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->after(function () {
                activity()
                    ->performedOn($this->record)
                    ->log("Record {$this->record->challan_number} was deleted by user");
                ChallanDeleted::dispatch($this->record);
                Notification::make()
                    ->title('Challan Deleted')
                    ->warning()
                    ->send()->afterClosingRedirectTo($this->redirectRoute('challans.index'));
            }),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
