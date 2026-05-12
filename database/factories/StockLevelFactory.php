<?php

namespace Database\Factories;

use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\StockLevel;
use App\Domains\Master\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockLevel>
 */
class StockLevelFactory extends Factory
{
    protected $model = StockLevel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'warehouse_id' => Warehouse::factory(),
            'available_qty' => $this->faker->numberBetween(100, 1000),
            'reserved_qty' => 0,
            'valuation_method' => 'FIFO',
        ];
    }

    public function withReservation(): static
    {
        return $this->state(fn(array $attributes) => [
            'reserved_qty' => $this->faker->numberBetween(10, 100),
        ]);
    }

    public function lifo(): static
    {
        return $this->state(fn(array $attributes) => [
            'valuation_method' => 'LIFO',
        ]);
    }
}
