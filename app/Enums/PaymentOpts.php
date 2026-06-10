<?php

namespace App\Enums;

class PaymentOpts
{
    const CASH = 'Cash';

    const AC = 'A/C';

    const CARD = 'Card';

    const BANK_TRANSFER = 'Bank Transfer';

    const UPI = 'UPI';

    const CHEQUE = 'Cheque';

    const NEFT = 'NEFT';

    const RTGS = 'RTGS';

    const OTHER = 'Other';

    public static function options(): array
    {
        return [
            self::CASH => 'Cash',
            self::AC => 'A/C',
            self::CARD => 'Credit/Debit Card',
            self::BANK_TRANSFER => 'Bank Transfer',
            self::UPI => 'Mobile Payment (UPI)',
            self::NEFT => 'NEFT',
            self::RTGS => 'RTGS',
            self::CHEQUE => 'Cheque',
            self::OTHER => 'Other',
        ];
    }
}
