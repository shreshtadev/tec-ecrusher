<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends BModel
{
    public function stockLevels(): HasMany
    {
        return $this->hasMany(StockLevel::class);
    }

    public function productionEntries(): HasMany
    {
        return $this->hasMany(ProductionEntry::class);
    }

    public function challan_items()
    {
        return $this->hasMany(ChallanItem::class);
    }
}
