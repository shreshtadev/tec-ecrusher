<?php

namespace App\Domains\Master\Models;

use App\Domains\Accounting\Models\Voucher;
use App\Domains\Common\Models\LModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends LModel
{
    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }
}
