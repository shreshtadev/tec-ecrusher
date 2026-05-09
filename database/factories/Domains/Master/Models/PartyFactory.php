<?php

namespace Database\Factories\Domains\Master\Models;

use App\Domains\Master\Models\Party;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Party>
 */
class PartyFactory extends Factory
{
    protected $model = Party::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => fake()->company(),
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => fake()->secondaryAddress(),
            'city' => fake()->city(),
            'state' => 'KA',
            'postal_code' => fake()->postcode(),
            'contact_number' => fake()->numerify('9#########'),
            'party_type' => fake()->randomElement([
                'Customer',
                'Supplier',
                'Other',
            ]),
        ];
    }
}
