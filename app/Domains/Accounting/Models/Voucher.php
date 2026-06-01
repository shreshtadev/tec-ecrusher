<?php

namespace App\Domains\Accounting\Models;

use App\Domains\Accounting\Events\PaymentMade;
use App\Domains\Accounting\Events\PaymentCollected;
use App\Domains\Common\Enums\DocOpts;
use App\Domains\Common\Models\SModel;
use App\Domains\Common\Services\DocumentNumberGenerator;
use App\Domains\Master\Models\Party;
use App\Domains\Operations\Models\Invoice;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected static function booted()
    {
        static::created(function ($voucher) {
            if (!$voucher->voucher_number) {
                $voucher->voucher_number = DocumentNumberGenerator::generate(
                    $voucher->invoice->company,
                    DocOpts::Voucher
                );
            }
        });

        static::saved(function ($voucher) {
            if ($voucher->voucher_type === 'Receipt') {
                return PaymentCollected::dispatch($voucher);
            }
            return PaymentMade::dispatch($voucher);
        });
    }
}
