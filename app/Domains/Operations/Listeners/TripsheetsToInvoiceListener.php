<?php

namespace App\Domains\Operations\Listeners;

use App\Domains\Operations\Events\ChallansFinalized;
use App\Domains\Operations\Services\StockService;

class TripsheetsToInvoiceListener
{
    public function __construct(protected StockService $stockService) {}
    public function handle(ChallansFinalized $event)
    {
        $tripsheets = $event->challans;
        $driverBatas = $event->driverBatas;
        $invoice = $this->stockService->createFromChallans($tripsheets, $driverBatas);

        // Finalize the invoice after creation
        $this->stockService->finalize($invoice);
    }
}
