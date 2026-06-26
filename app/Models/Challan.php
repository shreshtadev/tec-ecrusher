<?php

namespace App\Models;

use App\Enums\DocOpts;
use App\Services\DocumentNumberGenerator;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Challan extends SModel
{
    protected static function booted(): void
    {
        static::creating(function (self $challan) {
            if (! $challan->challan_number) {
                $challan->challan_number =
                    DocumentNumberGenerator::generate(
                        $challan->company,
                        DocOpts::Challan
                    );
            }
        });
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    // Link to the resulting Invoice
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Link to Stock Reservation
    public function stockReservation(): HasOne
    {
        return $this->hasOne(StockReservation::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function challan_items()
    {
        return $this->hasMany(ChallanItem::class);
    }

    /**
     * Get all items associated with this challan through challan_items.
     */
    public function items(): HasManyThrough
    {
        return $this->hasManyThrough(
            Item::class,
            ChallanItem::class,
            'challan_id',  // Foreign key on challan_items table
            'id',          // Foreign key on items table
            'id',          // Local key on challans table
            'item_id'      // Local key on challan_items table
        );
    }
}
