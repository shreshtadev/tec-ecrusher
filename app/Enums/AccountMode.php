<?php

namespace App\Enums;

enum AccountMode: string
{
    case Cash = 'cash';
    case Bank = 'bank';
    case Ledger = 'ledger';
}
