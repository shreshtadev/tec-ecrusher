<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // $this->call([
        //     TestDataSeeder::class,
        // ]);

        // $superAdmin = User::firstWhere('email', env('USER_ADMIN_ACCESS'));
        // $superAdmin->assignRole('super_admin');
    }
}
