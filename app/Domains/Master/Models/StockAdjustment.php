<?php

namespace App\Domains\Master\Models;

use App\Domains\Common\Models\BModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockAdjustment extends BModel
{
    protected $fillable = [
        'item_id',
        'warehouse_id',
        'quantity_change',
        'adjustment_type',
        'reason',
        'reference_number',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function adjustment(): MorphTo
    {
        return $this->morphTo();
    }
}
