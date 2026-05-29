<?php

namespace App\Domains\Operations\Models;

use App\Domains\Common\Models\SModel;
use App\Domains\Master\Models\Party;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends SModel
{

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
}
