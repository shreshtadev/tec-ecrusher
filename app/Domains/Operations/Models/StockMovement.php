<?php

namespace App\Domains\Operations\Models;

use App\Domains\Common\Models\SModel;
use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\Warehouse;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends SModel
{
    protected $fillable = [
        'item_id',
        'warehouse_id',
        'source_id',
        'source_type',
        'movement_type',
        'quantity',
        'unit_cost',
        'movement_date',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_cost' => 'decimal:2',
            'movement_date' => 'datetime',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
