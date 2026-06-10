<?php

namespace App\Listeners;

use App\Events\ChallanFinalized;
use App\Models\Invoice;
use App\Services\StockService;
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
                'total_amount' => $challan->challan_items->sum('amount') + $challan->driver_bata,
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
