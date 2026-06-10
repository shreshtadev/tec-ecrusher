<?php

namespace App\Listeners;

use App\Events\VoucherSaved;
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
        Log::info('A '.$event->voucher->type.' voucher has been saved with ID: '.$event->voucher->voucher_number);
    }
}
