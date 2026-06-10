<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends BModel
{
    protected $fillable = [
        'material_name',
        'price_per_unit',
        'unit',
    ];

    public function stockLevels(): HasMany
    {
        return $this->hasMany(StockLevel::class);
    }

    public function productionEntries(): HasMany
    {
        return $this->hasMany(ProductionEntry::class);
    }
}
