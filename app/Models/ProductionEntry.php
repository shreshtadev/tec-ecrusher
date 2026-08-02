<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionEntry extends SModel
{
    use SoftDeletes;
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
