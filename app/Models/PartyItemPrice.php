<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class PartyItemPrice extends SModel
{
    use SoftDeletes;
    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
