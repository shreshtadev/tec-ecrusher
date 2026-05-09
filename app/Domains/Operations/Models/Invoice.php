<?php

namespace App\Domains\Operations\Models;

use App\Domains\Accounting\Models\LedgerEntry;
use App\Domains\Common\Models\SModel;
use App\Domains\Master\Models\Party;

class Invoice extends SModel
{
    // An invoice can cover multiple challans (Trip Sheets)
    public function challans()
    {
        return $this->hasMany(Challan::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    // Polymorphic link to Accounting (for tracking the financial impact)
    public function ledgerEntries()
    {
        return $this->morphMany(LedgerEntry::class, 'recordable');
    }
}
