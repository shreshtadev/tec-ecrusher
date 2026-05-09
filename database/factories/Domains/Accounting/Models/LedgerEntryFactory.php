<?php

namespace Database\Factories\Domains\Accounting\Models;

use App\Domains\Accounting\Models\LedgerEntry;
use App\Domains\Master\Models\Party;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LedgerEntry>
 */
class LedgerEntryFactory extends Factory
{
    protected $model = LedgerEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'entry_date' => fake()->date(),

            'party_id' => Party::factory(),

            'recordable_type' => null,

            'recordable_id' => null,

            'description' => fake()->sentence(),

            'debit' => fake()->randomFloat(2, 0, 100000),

            'credit' => fake()->randomFloat(2, 0, 100000),

            'balance' => fake()->randomFloat(2, 0, 500000),
        ];
    }
}
