<?php

namespace App\Models;


class PartyItemPrice extends BModel
{
    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
