<?php

namespace App\Domains\Operations\Models;

use App\Domains\Common\Models\BModel;
use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\Warehouse;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockReservation extends BModel
{
    protected $fillable = [
        'challan_id',
        'warehouse_id',
        'item_id',
        'quantity_reserved',
        'status',
    ];

    public function challan(): BelongsTo
    {
        return $this->belongsTo(Challan::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
