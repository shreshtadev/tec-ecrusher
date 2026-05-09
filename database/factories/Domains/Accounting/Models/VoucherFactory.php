<?php

namespace Database\Factories\Domains\Accounting\Models;

use App\Domains\Accounting\Models\Voucher;
use App\Domains\Master\Models\Party;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Voucher>
 */
class VoucherFactory extends Factory
{
    protected $model = Voucher::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'voucher_no' =>
            'VCH-' . fake()->unique()->numberBetween(10000, 99999),

            'voucher_date' => fake()->date(),

            'party_id' => Party::factory(),

            'voucher_type' => fake()->randomElement([
                'Payment',
                'Receipt',
            ]),

            'amount' => fake()->randomFloat(2, 1000, 200000),

            'payment_mode' => fake()->randomElement([
                'Cash',
                'UPI',
                'Bank Transfer',
            ]),

            'remarks' => fake()->sentence(),

            'reference_invoice_id' => null,
        ];
    }
}
