<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends SModel
{
    use SoftDeletes;
    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }
}
