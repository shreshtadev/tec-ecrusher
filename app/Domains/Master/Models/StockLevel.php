<?php

namespace App\Domains\Master\Models;

use App\Domains\Common\Models\BModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLevel extends BModel
{
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
