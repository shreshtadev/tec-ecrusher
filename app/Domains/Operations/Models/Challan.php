<?php

namespace App\Domains\Operations\Models;

use App\Domains\Common\Models\SModel;
use App\Domains\Master\Models\Driver;
use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\Party;
use App\Domains\Master\Models\Vehicle;

class Challan extends SModel
{
    public function party()
    {
        return $this->belongsTo(Party::class);
    }
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // Link to the resulting Invoice
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
