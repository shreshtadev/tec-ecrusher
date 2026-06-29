<?php

namespace App\Observers;

use App\Models\InvoiceItem;
use App\Models\Item;
use App\Models\Warehouse;
use App\Services\StockService;

class InvoiceItemObserver
{
    public function __construct(private StockService $stockService)
    {
        //
    }
    public function created(InvoiceItem $invoiceItem): void
    {
        $this->updateStock($invoiceItem);
        $this->updateInvoiceTotal($invoiceItem);
    }

    public function updated(InvoiceItem $invoiceItem): void
    {
        $this->updateStock($invoiceItem);
        $this->updateInvoiceTotal($invoiceItem);
    }

    public function deleted(InvoiceItem $invoiceItem): void
    {
        $this->updateInvoiceTotal($invoiceItem);
    }

    private function updateInvoiceTotal(InvoiceItem $invoiceItem): void
    {
        $invoice = $invoiceItem->invoice;
        $totalAmount = $invoice->invoiceItems()->sum('amount');
        $grandTotal = $totalAmount - $invoice->discount_amount;

        $invoice->update([
            'total_amount' => $totalAmount,
            'grand_total' => $grandTotal,
        ]);
        $invoice->refresh();
    }

    private function updateStock(InvoiceItem $invoiceItem): void
    {
        $invoice = $invoiceItem->invoice;
        $partyType = $invoice->party->party_type;
        if ('Supplier' == $partyType) {
            $foundItem = Item::findOrFail($invoiceItem->item_id);
            $foundWarehouse = Warehouse::findOrFail($invoiceItem->warehouse_id);
            $this->stockService->receiveStock($foundItem, $foundWarehouse, $invoiceItem->quantity, $foundItem->price_per_unit);
        }
    }
}
