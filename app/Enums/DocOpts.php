<?php

namespace App\Enums;

enum DocOpts: string
{
    case PartyAccount = 'party_account';
    case Challan = 'challan';
    case CompanyAccount = 'company_account';
    case Invoice = 'invoice';
    case Voucher = 'voucher';
}
