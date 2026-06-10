<?php

namespace App\Listeners;

use App\Events\ChallanDeleted;
use App\Services\StockService;
use Exception;

class ChallanDeletedListener
{
    public function __construct(private StockService $stockService)
    {
        //
    }

    public function handle(ChallanDeleted $event)
    {
        $challan = $event->challan;
        try {
            $this->stockService->unreserve($challan);
        } catch (Exception $e) {
            report($e);
            throw $e;
        }
    }
}
