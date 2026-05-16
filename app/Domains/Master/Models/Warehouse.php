<?php

namespace App\Domains\Master\Models;

use App\Domains\Common\Models\BModel;
use App\Domains\Operations\Models\ProductionEntry;
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
}
