<?php

namespace Database\Factories\Domains\Master\Models;

use App\Domains\Master\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'material_name' => fake()->randomElement([
                '20mm Jelly',
                '40mm Jelly',
                'M-Sand',
                'P-Sand',
                'Dust',
                '6mm Jelly',
            ]),

            'price_per_unit' => fake()->randomFloat(2, 20, 100),

            'unit' => 'CFT',
        ];
    }
}
