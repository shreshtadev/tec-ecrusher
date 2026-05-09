<?php

namespace App\Domains\Accounting\Models;

use App\Domains\Accounting\Events\VoucherSaved;
use App\Domains\Common\Models\SModel;
use App\Domains\Master\Models\Party;
use App\Domains\Operations\Models\Invoice;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

class Voucher extends SModel
{
    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'reference_invoice_id');
    }

    #[Override]
    protected static function booted()
    {
        static::saved(fn($voucher) => VoucherSaved::dispatch($voucher));
    }
}
