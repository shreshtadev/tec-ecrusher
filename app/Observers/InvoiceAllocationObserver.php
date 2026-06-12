<?php

namespace App\Observers;

use App\Models\InvoiceAllocation;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class InvoiceAllocationObserver implements ShouldHandleEventsAfterCommit
{
    public function created(InvoiceAllocation $allocation): void
    {
        $allocation->invoice->recalculateOutstanding();
    }

    public function updated(InvoiceAllocation $allocation): void
    {
        $allocation->invoice->recalculateOutstanding();
    }

    public function deleted(InvoiceAllocation $allocation): void
    {
        $allocation->invoice->recalculateOutstanding();
    }
}
