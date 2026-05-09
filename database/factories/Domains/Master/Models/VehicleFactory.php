<?php

namespace Database\Factories\Domains\Master\Models;

use App\Domains\Master\Models\Party;
use App\Domains\Master\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'party_id' => Party::factory(),

            'vehicle_number' =>
            'KA-' .
                fake()->numberBetween(1, 99) .
                '-' .
                strtoupper(fake()->lexify('??')) .
                '-' .
                fake()->numberBetween(1000, 9999),

            'capacity_cft' => fake()->randomFloat(2, 100, 1000),

            'vehicle_type' => fake()->randomElement([
                'Truck',
                'Tipper',
                'Lorry',
                'Mini Truck',
            ]),
        ];
    }
}
