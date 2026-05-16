<?php

namespace App\Domains\Common\Events;

use App\Domains\Accounting\Models\Voucher;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoucherSaved
{

    use Dispatchable, SerializesModels, InteractsWithSockets;
    public function __construct(public Voucher $voucher) {}
}
