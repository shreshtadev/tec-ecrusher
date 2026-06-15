<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Party extends BModel
{
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class);
    }

    // Links to Operations
    public function challans(): HasMany
    {
        return $this->hasMany(Challan::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function itemPrices()
    {
        return $this->hasMany(PartyItemPrice::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
