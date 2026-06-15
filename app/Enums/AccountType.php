<?php

namespace App\Enums;

enum AccountType: string
{
    case ASSET = 'asset';
    case LIABILITY = 'liability';
    case EQUITY = 'equity';
    case INCOME = 'income';
    case EXPENSE = 'expense';

    public static function displayValz(AccountType $vl): string
    {
        return ucfirst(str_replace('_', ' ', $vl->value));
    }
}
