<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends SModel
{
    protected $casts = [
        'expenditure_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }
}
