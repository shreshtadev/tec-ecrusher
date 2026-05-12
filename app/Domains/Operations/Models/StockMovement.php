<?php

namespace App\Domains\Operations\Models;

use App\Domains\Common\Models\SModel;
use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\StockAdjustment;
use App\Domains\Master\Models\Warehouse;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends SModel
{
    protected $fillable = [
        'item_id',
        'warehouse_id',
        'challan_id',
        'invoice_id',
        'adjustment_id',
        'movement_type',
        'quantity',
        'unit_cost',
        'notes',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function challan(): BelongsTo
    {
        return $this->belongsTo(Challan::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function adjustment(): BelongsTo
    {
        return $this->belongsTo(StockAdjustment::class);
    }
}
