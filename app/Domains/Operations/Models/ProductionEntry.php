<?php

namespace App\Domains\Operations\Models;

use App\Domains\Common\Models\SModel;
use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\Warehouse;

class ProductionEntry extends SModel
{
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
