<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $accounts = [
            [
                'full_name'      => 'Cash in Hand',
                'account_number' => null,
                'bank_name'      => null,
                'branch_code'    => null,
                'account_type'   => 'asset',
                'account_mode'   => 'cash',
                'balance'        => 0,
                'is_active'      => true,
            ],
            [
                'full_name'      => 'HDFC Current Account',
                'account_number' => '50100123456789',
                'bank_name'      => 'HDFC Bank',
                'branch_code'    => 'HDFC0001234',
                'account_type'   => 'asset',
                'account_mode'   => 'bank',
                'balance'        => 0,
                'is_active'      => true,
            ],
            [
                'full_name'      => 'ICICI Current Account',
                'account_number' => '123456789012',
                'bank_name'      => 'ICICI Bank',
                'branch_code'    => 'ICIC0000456',
                'account_type'   => 'asset',
                'account_mode'   => 'bank',
                'balance'        => 0,
                'is_active'      => true,
            ],
            [
                'full_name'      => 'Accounts Receivable',
                'account_number' => 'AR001',
                'bank_name'      => null,
                'branch_code'    => null,
                'account_mode'   => 'ledger',
                'account_type'   => 'asset',
                'balance'        => 0,
                'is_active'      => true,
            ],
            [
                'full_name'      => 'Accounts Payable',
                'account_number' => 'AP001',
                'bank_name'      => null,
                'branch_code'    => null,
                'account_mode'   => 'ledger',
                'account_type'   => 'liability',
                'balance'        => 0,
                'is_active'      => true,
            ],
            [
                'full_name'      => 'Owner Capital',
                'account_number' => null,
                'bank_name'      => null,
                'branch_code'    => null,
                'account_type'   => 'equity',
                'account_mode'   => 'ledger',
                'balance'        => 0,
                'is_active'      => true,
            ],
        ];

        foreach ($accounts as $account) {
            Account::updateOrCreate(
                [
                    'full_name' => $account['full_name'],
                ],
                $account
            );
        }
    }
}
