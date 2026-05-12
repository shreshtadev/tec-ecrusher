<?php

namespace App\Domains\Operations\Listeners;

use App\Domains\Operations\Events\InvoiceCreated;
use App\Domains\Operations\Services\StockService;
use Exception;

class InvoiceCreatedListener
{
    public function __construct(private StockService $stockService)
    {
        //
    }

    public function handle(InvoiceCreated $event): void
    {
        try {
            $this->stockService->finalize($event->invoice);
        } catch (Exception $e) {
            report($e);
            throw $e;
        }
    }
}
