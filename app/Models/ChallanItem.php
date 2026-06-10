<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChallanItem extends LModel
{
    protected $fillable = [
        'challan_id',
        'item_id',
        'quantity_cft',
        'rate_at_sale',
        'amount',
    ];

    public function challan(): BelongsTo
    {
        return $this->belongsTo(Challan::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
