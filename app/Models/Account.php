<?php

namespace App\Models;

class Account extends LModel
{
    public function outboundVouchers()
    {
        return $this->hasMany(Voucher::class, 'from_account_id');
    }

    public function inboundVouchers()
    {
        return $this->hasMany(Voucher::class, 'to_account_id');
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }
}
