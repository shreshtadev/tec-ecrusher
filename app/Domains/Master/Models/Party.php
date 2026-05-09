<?php

namespace App\Domains\Master\Models;

use App\Domains\Accounting\Models\LedgerEntry;
use App\Domains\Common\Models\BModel;
use App\Domains\Operations\Models\Challan;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Party extends BModel
{

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class);
    }

    // Links to Operations
    public function challans(): HasMany
    {
        return $this->hasMany(Challan::class);
    }

    // Links to Accounting
    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(LedgerEntry::class);
    }
}
