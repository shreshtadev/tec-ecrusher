<?php

namespace App\Domains\Operations\Events;

use App\Domains\Operations\Models\Challan;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChallanFinalized
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Challan $challan)
    {
        //
    }
}
