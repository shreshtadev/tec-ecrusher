<?php

namespace App\Domains\Common\Enums;


class VoucherOpts
{
    const PAYMENT = 'Payment';
    const RECEIPT = 'Receipt';
    const JOURNAL = 'Journal';

    public static function options(): array
    {
        return [
            self::PAYMENT => 'Payment',
            self::RECEIPT => 'Receipt',
            self::JOURNAL => 'Journal',
        ];
    }
}
