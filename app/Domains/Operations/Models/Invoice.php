<?php

namespace App\Domains\Operations\Models;

use App\Domains\Common\Enums\DocOpts;
use App\Domains\Common\Models\SModel;
use App\Domains\Common\Services\DocumentNumberGenerator;
use App\Domains\Master\Models\Company;
use App\Domains\Master\Models\Party;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends SModel
{
    protected static function booted(): void
    {
        static::creating(function (self $invoice) {
            if (!$invoice->invoice_number) {
                $invoice->invoice_number = DocumentNumberGenerator::generate(
                    $invoice->company,
                    DocOpts::Invoice
                );
            }
        });
    }
    // An invoice can cover multiple challans (Trip Sheets)
    public function challans(): HasMany
    {
        return $this->hasMany(Challan::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    // Link to Stock Movements
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
