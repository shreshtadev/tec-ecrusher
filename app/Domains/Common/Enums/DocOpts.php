<?php

namespace App\Domains\Common\Enums;

enum DocOpts: string
{
    case Challan = 'challan';
    case Invoice = 'invoice';
    case Voucher = 'voucher';
}
