<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Shruthi Stone Crusher',
                'address' => 'Nagarahalli Village, Chikkamagaluru - 577101'
            ]
        );
    }
}
