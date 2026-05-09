<?php

namespace App\Domains\Accounting\Events;

use App\Domains\Accounting\Models\Voucher;
use Illuminate\Foundation\Events\Dispatchable;

class VoucherSaved
{
    use Dispatchable;
    public function __construct(public Voucher $voucher) {}
}
