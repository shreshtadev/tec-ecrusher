<?php

namespace App\Domains\Accounting\Listeners;

use App\Domains\Accounting\Models\LedgerEntry;
use App\Domains\Operations\Events\ChallanFinalized;
use App\Domains\Operations\Models\Invoice;
use App\Domains\Operations\Services\StockService;
use Illuminate\Support\Facades\DB;

class SyncInvoiceToLedger
{
    public function __construct(private StockService $stockService) {}

    public function handle(ChallanFinalized $event): void
    {
        $challan = $event->challan;

        DB::transaction(function () use ($challan) {
            // 1. Create the Invoice (The "Operations" part of the slice)
            $invoice = Invoice::create([
                'invoice_number' => 'INV-'.str_pad($challan->id, 6, '0', STR_PAD_LEFT),
                'party_id' => $challan->party_id,
                'total_amount' => $challan->quantity_cft * $challan->item->price_per_unit,
                'driver_bata' => 0,
                'payment_mode' => $challan->payment_mode ?? 'Credit',
            ]);

            // 2. Mark Challan as Invoiced
            $challan->update([
                'invoice_id' => $invoice->id,
                'status' => 'Invoiced',
            ]);

            $invoice->load('challans.stockReservation.warehouse', 'challans.stockReservation.item');
            $this->stockService->finalize($invoice);

            // 3. Sync to Ledger (The "Accounting" part of the slice)
            LedgerEntry::create([
                'entry_date' => now()->toDateString(),
                'party_id' => $challan->party_id,
                'recordable_type' => Invoice::class,
                'recordable_id' => $invoice->id,
                'description' => "Invoice #{$invoice->invoice_number} generated from Challan #{$challan->challan_number}",
                'debit' => $invoice->total_amount,
                'credit' => 0,
                'balance' => 0, // Calculated by your reporting service later
            ]);
        });
    }
}
