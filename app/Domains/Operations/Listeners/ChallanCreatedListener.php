<?php

namespace App\Domains\Operations\Listeners;

use App\Domains\Operations\Events\ChallanCreated;
use App\Domains\Operations\Services\StockService;
use Exception;

class ChallanCreatedListener
{
    public function __construct(private StockService $stockService)
    {
        //
    }

    public function handle(ChallanCreated $event): void
    {
        try {
            $this->stockService->reserve($event->challan);
        } catch (Exception $e) {
            // Log error or handle appropriately
            report($e);
            throw $e;
        }
    }
}
