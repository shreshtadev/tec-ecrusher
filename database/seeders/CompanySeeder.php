<?php

namespace Database\Seeders;

use App\Domains\Master\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Shruthi Stone Crusher',
            ]
        );
    }
}
