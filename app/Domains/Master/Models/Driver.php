<?php

namespace App\Domains\Master\Models;

use App\Domains\Common\Models\BModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Driver extends BModel
{
    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }
}
