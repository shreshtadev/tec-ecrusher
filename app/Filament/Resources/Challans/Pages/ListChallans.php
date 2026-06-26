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
                ->failureNotification(Notification::make()
                    ->danger()
                    ->title('Tripsheet/Stock Error')
                    ->body('We could not save your submission. Please check inventory.'), )->before(function (Action $action) {
                        $data = $action->getLivewire()->form->getState();

                        $itemId = $data['item_id'] ?? null;
                        $quantity = $data['quantity_cft'] ?? null;

                        if ($itemId && $quantity) {
                            $availableStock = StockLevel::where('item_id', $itemId)->pluck('available_qty')->first();

                            if ($quantity > $availableStock) {
                                return false; // Prevent form submission
                            }
                        }

                        return true; // Allow form submission
                    })->visible(
                        fn () => StockLevel::where('available_qty', '>', 0)->exists()
                    ),
        ];
    }
}
