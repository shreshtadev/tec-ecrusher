<?php

namespace App\Events;

use App\Models\Voucher;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoucherSaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Voucher $voucher) {}
}
