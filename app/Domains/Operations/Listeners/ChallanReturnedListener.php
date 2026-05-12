<?php

namespace App\Domains\Operations\Listeners;

use App\Domains\Operations\Events\ChallanReturned;
use App\Domains\Operations\Services\StockService;
use Exception;

class ChallanReturnedListener
{
    public function __construct(private StockService $stockService)
    {
        //
    }

    public function handle(ChallanReturned $event): void
    {
        try {
            $this->stockService->handleReturn($event->challan, $event->returnQuantity);
        } catch (Exception $e) {
            report($e);
            throw $e;
        }
    }
}
