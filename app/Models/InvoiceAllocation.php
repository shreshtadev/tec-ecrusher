<?php

namespace App\Models;

class InvoiceAllocation extends BModel
{
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}
