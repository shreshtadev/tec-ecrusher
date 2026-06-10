<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Driver extends BModel
{
    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }
}
