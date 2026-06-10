<?php

namespace App\Models;

use App\Enums\DocOpts;
use App\Events\PaymentCollected;
use App\Events\PaymentMade;
use App\Services\DocumentNumberGenerator;
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

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    protected static function booted()
    {
        static::creating(function ($voucher) {
            if (! $voucher->voucher_no) {
                $voucher->voucher_no = DocumentNumberGenerator::generate(
                    $voucher->company,
                    DocOpts::Voucher
                );
            }
        });

        static::saved(function ($voucher) {
            if ($voucher->voucher_type === 'Receipt') {
                PaymentCollected::dispatch($voucher);
            }
            PaymentMade::dispatch($voucher);
        });
    }
}
