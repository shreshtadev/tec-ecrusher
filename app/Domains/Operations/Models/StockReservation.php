<?php

namespace App\Domains\Operations\Models;

use App\Domains\Common\Models\BModel;
use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\Warehouse;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockReservation extends BModel
{
    protected $fillable = [
        'source_id',
        'source_type',
        'warehouse_id',
        'item_id',
        'quantity',
        'status',
        'reserved_at',
        'finalized_at',
        'cancelled_at',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'reserved_at' => 'datetime',
            'finalized_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }
}
