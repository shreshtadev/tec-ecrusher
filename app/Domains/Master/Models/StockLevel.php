<?php

namespace App\Domains\Master\Models;

use App\Domains\Common\Models\BModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLevel extends BModel
{
    protected $fillable = [
        'item_id',
        'warehouse_id',
        'available_qty',
        'reserved_qty',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
