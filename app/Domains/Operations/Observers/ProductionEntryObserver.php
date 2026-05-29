<?php

namespace App\Domains\Operations\Observers;

use App\Domains\Operations\Models\ProductionEntry;
use App\Domains\Operations\Services\StockService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class ProductionEntryObserver implements ShouldHandleEventsAfterCommit
{
    public function __construct(private StockService $stockService) {}

    /**
     * Handle the ProductionEntry "created" event.
     */
    public function created(ProductionEntry $productionEntry): void
    {
        $this->stockService->createOnProductionEntry($productionEntry);
    }

    /**
     * Handle the ProductionEntry "updated" event.
     */
    public function updated(ProductionEntry $productionEntry): void
    {
        $this->stockService->updateOnProductionEntry($productionEntry);
    }

    /**
     * Handle the ProductionEntry "deleted" event.
     */
    public function deleted(ProductionEntry $productionEntry): void
    {
        // Intentionally left blank until reversal logic exists in StockService.
    }

    /**
     * Handle the ProductionEntry "restored" event.
     */
    public function restored(ProductionEntry $productionEntry): void
    {
        // Intentionally left blank until restore logic exists in StockService.
    }

    /**
     * Handle the ProductionEntry "force deleted" event.
     */
    public function forceDeleted(ProductionEntry $productionEntry): void
    {
        // Intentionally left blank until permanent delete logic exists in StockService.
    }
}
