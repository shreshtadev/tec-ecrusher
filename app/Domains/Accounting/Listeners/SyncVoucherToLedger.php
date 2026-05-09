<?php

namespace App\Domains\Accounting\Listeners;

use App\Domains\Accounting\Events\VoucherSaved;
use App\Domains\Accounting\Models\LedgerEntry;
use App\Domains\Accounting\Models\Voucher;

class SyncVoucherToLedger
{
    public function handle(VoucherSaved $event): void
    {
        $v = $event->voucher;

        LedgerEntry::create([
            'entry_date' => $v->voucher_date,
            'party_id' => $v->party_id,
            'recordable_type' => Voucher::class,
            'recordable_id' => $v->id,
            'description' => "{$v->type} via {$v->payment_mode}" . ($v->remarks ? ": {$v->remarks}" : ""),
            // If Receipt: Credit the party (they paid us). If Payment: Debit the party.
            'credit' => $v->type === 'Receipt' ? $v->amount : 0,
            'debit' => $v->type === 'Payment' ? $v->amount : 0,
            'balance' => 0, // Calculated during reporting
        ]);
    }
}
