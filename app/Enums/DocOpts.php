<?php

namespace App\Enums;

enum DocOpts: string
{
    case Challan = 'challan';
    case Invoice = 'invoice';
    case Voucher = 'voucher';
}
