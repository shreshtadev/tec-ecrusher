<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockReservation extends BModel
{

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
