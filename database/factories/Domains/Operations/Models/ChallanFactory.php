<?php

namespace Database\Factories\Domains\Operations\Models;

use App\Domains\Master\Models\Driver;
use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\Party;
use App\Domains\Master\Models\Vehicle;
use App\Domains\Operations\Models\Challan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Challan>
 */
class ChallanFactory extends Factory
{
    protected $model = Challan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'challan_number' =>
            'CH-' . fake()->unique()->numberBetween(10000, 99999),

            'party_id' => Party::factory(),

            'vehicle_id' => Vehicle::factory(),

            'driver_id' => Driver::factory(),

            'item_id' => Item::factory(),

            'quantity_cft' => fake()->randomFloat(2, 50, 500),

            'status' => fake()->randomElement([
                'Pending',
                'Invoiced',
            ]),
        ];
    }
}
