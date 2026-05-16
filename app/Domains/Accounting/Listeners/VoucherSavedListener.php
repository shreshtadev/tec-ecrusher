<?php

namespace App\Domains\Accounting\Listeners;

use App\Domains\Common\Events\VoucherSaved;
use Illuminate\Support\Facades\Log;

class VoucherSavedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(VoucherSaved $event): void
    {
        if ($event->voucher->type === 'Receipt') {
            Log::info('A receipt voucher has been saved with ID: ' . $event->voucher->id);
        }

        if ($event->voucher->type === 'Payment') {
            Log::info('A payment voucher has been saved with ID: ' . $event->voucher->id);
        }
    }
}
