<?php

namespace App\Enums;

class VoucherOpts
{
    const PAYMENT = 'payment';

    const RECEIPT = 'receipt';

    const JOURNAL = 'journal';
    const CREDIT_NOTE = 'credit_note';
    const DEBIT_NOTE = 'debit_note';

    public static function options(): array
    {
        return [
            self::PAYMENT => 'payment',
            self::RECEIPT => 'receipt',
            self::JOURNAL => 'journal',
            self::CREDIT_NOTE => 'credit_note',
            self::DEBIT_NOTE => 'debit_note',
        ];
    }
}
