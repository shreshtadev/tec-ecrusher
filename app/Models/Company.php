<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends LModel
{
    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }
}
