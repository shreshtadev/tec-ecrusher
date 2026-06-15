<?php

namespace App\Enums;

enum AccountMode: string
{
    case Cash = 'cash';
    case Bank = 'bank';
    case Ledger = 'ledger';

    public static function displayValz(AccountMode $vl): string
    {
        return ucfirst(str_replace('_', ' ', $vl->value));
    }
}
