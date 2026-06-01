<?php

namespace App\Domains\Operations\Models;

use App\Domains\Common\Enums\DocOpts;
use App\Domains\Common\Models\SModel;
use App\Domains\Common\Services\DocumentNumberGenerator;
use App\Domains\Master\Models\Company;
use App\Domains\Master\Models\Driver;
use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\Party;
use App\Domains\Master\Models\Vehicle;
use App\Domains\Operations\Events\ChallanCreated;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Challan extends SModel
{
    protected static function booted(): void
    {
        static::creating(function (self $challan) {
            if (!$challan->challan_number) {
                $challan->challan_number =
                    DocumentNumberGenerator::generate(
                        $challan->company,
                        DocOpts::Challan
                    );
            }
        });
        static::created(function (self $challan) {
            ChallanCreated::dispatch($challan);
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

    public function item()
    {
        return $this->belongsTo(Item::class);
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
}
