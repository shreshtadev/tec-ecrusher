<?php

namespace App\Filament\Resources\Challans\Pages;

use App\Filament\Resources\Challans\ChallanResource;
use App\Models\StockLevel;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListChallans extends ListRecords
{
    protected static string $resource = ChallanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->before(function (Action $action) {
                    $data = $action->getLivewire()->form->getState();

                    $itemId = $data['item_id'] ?? null;
                    $quantity = $data['quantity_cft'] ?? null;

                    if ($itemId && $quantity) {
                        $availableStock = (float) StockLevel::query()
                            ->where('item_id', $itemId)
                            ->sum('available_qty');

                        if ($quantity > $availableStock) {
                            // Send the failure notification manually before halting
                            Notification::make()
                                ->danger()
                                ->title('Tripsheet/Stock Error')
                                ->body('We could not save your submission. Please check inventory.')
                                ->send();

                            // Halt the execution and keep the modal open
                            $action->halt();
                        }
                    }

                    // Do not return false here. Leaving it blank allows execution to continue.
                })
                ->visible(
                    fn() => StockLevel::where('available_qty', '>', 0)->exists()
                ),
        ];
    }
}
