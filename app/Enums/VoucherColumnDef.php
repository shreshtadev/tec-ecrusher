<?php

namespace App\Enums;

class VoucherColumnDef
{
    public static function columns(): array
    {
        return [
            'voucher_no' => 'Voucher Number',
            'voucher_date' => 'Voucher Date',
            'party.full_name' => 'Party Name',
            'voucher_type' => 'Voucher Type',
            'amount' => 'Amount',
            'payment_mode' => 'Payment Mode',
            'invoice.invoice_number' => 'Reference Invoice',
            'remarks' => 'Remarks',
            'created_at' => 'Created At',
        ];
    }
}
