<?php

namespace App\Models;

use App\Enums\DocOpts;
use App\Services\DocumentNumberGenerator;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends SModel
{
    use SoftDeletes;
    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
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

    public function allocations()
    {
        return $this->hasMany(InvoiceAllocation::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
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
    }
}
