<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends LModel
{
    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function challans(): HasMany
    {
        return $this->hasMany(Challan::class);
    }

    public function parties(): HasMany
    {
        return $this->hasMany(Party::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }
}
