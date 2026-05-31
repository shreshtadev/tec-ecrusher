<?php

namespace App\Domains\Accounting\Models;

use App\Domains\Common\Models\SModel;
use App\Domains\Master\Models\Party;
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
