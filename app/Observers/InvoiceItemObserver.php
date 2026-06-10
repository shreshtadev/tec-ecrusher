<?php

namespace App\Observers;

use App\Models\InvoiceItem;

class InvoiceItemObserver
{
    public function created(InvoiceItem $invoiceItem): void
    {
        $this->updateInvoiceTotal($invoiceItem);
    }

    public function updated(InvoiceItem $invoiceItem): void
    {
        $this->updateInvoiceTotal($invoiceItem);
    }

    public function deleted(InvoiceItem $invoiceItem): void
    {
        $this->updateInvoiceTotal($invoiceItem);
    }

    private function updateInvoiceTotal(InvoiceItem $invoiceItem): void
    {
        $invoice = $invoiceItem->invoice;

        $invoice->update([
            'total_amount' => $invoice->invoiceItems()->sum('amount'),
        ]);
    }
}
