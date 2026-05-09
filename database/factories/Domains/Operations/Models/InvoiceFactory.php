<?php

namespace Database\Factories\Domains\Operations\Models;

use App\Domains\Master\Models\Party;
use App\Domains\Operations\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_number' =>
            'INV-' . fake()->unique()->numberBetween(10000, 99999),

            'party_id' => Party::factory(),

            'total_amount' => fake()->randomFloat(2, 5000, 500000),

            'driver_bata' => fake()->randomFloat(2, 0, 5000),

            'payment_mode' => fake()->randomElement([
                'Cash',
                'UPI',
                'Bank Transfer',
                'Credit',
            ]),
        ];
    }
}
