<?php

namespace App\Models;

use App\Enums\DocOpts;
use App\Services\DocumentNumberGenerator;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends SModel
{
    protected static function booted(): void
    {
        static::creating(function (self $invoice) {
            if (! $invoice->invoice_number) {
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

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function allocations()
    {
        return $this->hasMany(InvoiceAllocation::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function recalculateOutstanding(): void
    {
        $allocated = $this->allocations()->sum('allocated_amount');
        $outstanding = ($this->total_amount + $this->driver_bata) - $allocated;

        $this->update([
            'outstanding_amount' => $outstanding,
            'payment_status' => match (true) {
                $outstanding <= 0 => 'paid',
                $allocated > 0 => 'partial',
                default => 'unpaid',
            },
        ]);
    }
}
