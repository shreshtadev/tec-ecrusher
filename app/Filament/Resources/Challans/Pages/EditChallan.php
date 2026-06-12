<?php

namespace App\Filament\Resources\Challans\Pages;

use App\Filament\Resources\Challans\ChallanResource;
use App\Services\StockService;
use Exception;
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
        $challan = $this->record;
        $stockService = app(StockService::class);
        return [
            DeleteAction::make()->after(function () use ($challan) {
                activity()
                    ->performedOn($this->record)
                    ->log("Record {$this->record->challan_number} was deleted by user");
                try {
                    $this->stockService->unreserve($challan);
                    Notification::make()
                        ->title('Challan Deleted')
                        ->warning()
                        ->send()->afterClosingRedirectTo($this->redirectRoute('challans.index'));
                } catch (Exception $e) {
                    report($e);
                    Notification::make()
                        ->title('Challan Deleted | Error')
                        ->warning()
                        ->send()->afterClosingRedirectTo($this->redirectRoute('challans.index'));
                }
            }),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
