<?php

namespace App\Domains\Operations\Listeners;

use App\Domains\Operations\Events\ChallanFinalized;
use App\Domains\Operations\Models\Invoice;
use App\Domains\Operations\Services\StockService;
use Illuminate\Support\Facades\DB;

class TripsheetToInvoiceListener
{
    public function __construct(private StockService $stockService) {}

    public function handle(ChallanFinalized $event): void
    {
        $challan = $event->challan;

        DB::transaction(function () use ($challan) {
            $invoice = Invoice::create([
                'party_id' => $challan->party_id,
                'total_amount' => $challan->amount,
                'driver_bata' => $challan->driver_bata,
                'payment_mode' => $challan->payment_mode,
                'company_id' => $challan->company_id,
            ]);

            $challan->update([
                'invoice_id' => $invoice->id,
                'status' => 'Invoiced',
            ]);
            $this->stockService->finalize($invoice);
        });
    }
}
