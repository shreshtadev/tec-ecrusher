<?php

namespace App\Events;

use App\Models\Challan;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChallanReturned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Challan $challan,
        public float $returnQuantity
    ) {
        //
    }
}
