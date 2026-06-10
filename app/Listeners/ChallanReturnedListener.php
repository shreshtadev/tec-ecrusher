<?php

namespace App\Listeners;

use App\Events\ChallanReturned;
use App\Services\StockService;
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
