<?php

namespace App\Domains\Operations\Events;

use App\Domains\Operations\Models\Invoice;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoiceCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Invoice $invoice)
    {
        //
    }
}
