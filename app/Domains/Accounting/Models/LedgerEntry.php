<?php

namespace App\Domains\Accounting\Models;

use App\Domains\Common\Models\SModel;

class LedgerEntry extends SModel
{
    public function party()
    {
        return $this->belongsTo(\App\Domains\Master\Models\Party::class);
    }

    public function recordable()
    {
        return $this->morphTo();
    }
}
