<?php

namespace App\Domains\Master\Models;

use App\Domains\Common\Models\BModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends BModel
{
    public function stockLevels(): HasMany
    {
        return $this->hasMany(StockLevel::class);
    }
}
