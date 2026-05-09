<?php

namespace Database\Factories\Domains\Master\Models;

use App\Domains\Master\Models\Driver;
use App\Domains\Master\Models\Party;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Driver>
 */
class DriverFactory extends Factory
{
    protected $model = Driver::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'party_id' => Party::factory(),

            'full_name' => fake()->name(),

            'phone_number' => fake()->numerify('9#########'),
        ];
    }
}
