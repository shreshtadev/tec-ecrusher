<?php

namespace App\Observers;

use App\Enums\VoucherOpts;
use App\Models\InvoiceAllocation;
use App\Models\Voucher;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class VoucherObserver implements ShouldHandleEventsAfterCommit
{
    public function created(Voucher $voucher): void
    {
        $this->syncAllocation($voucher);
    }

    public function updated(Voucher $voucher): void
    {
        $this->syncAllocation($voucher);
    }

    public function deleted(Voucher $voucher): void
    {
        InvoiceAllocation::where('voucher_id', $voucher->id)->delete();
    }

    private function syncAllocation(Voucher $voucher): void
    {
        InvoiceAllocation::where('voucher_id', $voucher->id)
            ->delete();

        if (! in_array($voucher->voucher_type, [
            VoucherOpts::RECEIPT,
            VoucherOpts::PAYMENT,
            VoucherOpts::CREDIT_NOTE,
            VoucherOpts::DEBIT_NOTE,
        ])) {
            return;
        }

        if (! $voucher->invoice_id) {
            return;
        }

        InvoiceAllocation::create([
            'voucher_id' => $voucher->id,
            'invoice_id' => $voucher->invoice_id,
            'allocated_amount' => $voucher->amount,
        ]);
    }
}
