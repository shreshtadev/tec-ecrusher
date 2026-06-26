<?php

namespace Database\Seeders;

use App\Enums\DocOpts;
use App\Models\Account;
use App\Models\Party;
use App\Services\DocumentNumberGenerator;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(3)->create();
        // $this->call([
        //     CompanySeeder::class
        // ]);

        $accounts = Party::doesntHave('accounts')
            ->get()
            ->map(function ($party) {
                return [
                    'title' => DocumentNumberGenerator::generate($party->company, DocOpts::PartyAccount),
                    'party_id'       => $party->id,
                    'account_number' => null,
                    'account_type'   => 'asset',
                    'account_mode'   => 'ledger',
                    'balance'        => 0,
                    'is_active'      => true,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                    'company_id'     => $party->company_id,
                ];
            })
            ->toArray();

        Account::insert($accounts);
    }
}
