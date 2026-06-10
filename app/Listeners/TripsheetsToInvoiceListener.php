<?php

namespace App\Listeners;

use App\Events\ChallansFinalized;
use App\Services\StockService;

class TripsheetsToInvoiceListener
{
    public function __construct(protected StockService $stockService) {}

    public function handle(ChallansFinalized $event)
    {
        $tripsheets = $event->challans;
        $invoice = $this->stockService->createFromChallans($tripsheets);

        // Finalize the invoice after creation
        $this->stockService->finalize($invoice);
    }
}
