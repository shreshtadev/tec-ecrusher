<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

use App\Domains\Operations\Models\Invoice;
use App\Domains\Accounting\Models\Voucher;
use App\Domains\Accounting\Models\LedgerEntry;
use App\Domains\Master\Models\Driver;
use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\Party;
use App\Domains\Master\Models\Vehicle;
use App\Domains\Operations\Models\Challan;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            $faker = Faker::create('en_IN');

            /*
            |--------------------------------------------------------------------------
            | ITEMS
            |--------------------------------------------------------------------------
            */

            $materials = [
                ['material_name' => '20mm Jelly', 'price_per_unit' => 45],
                ['material_name' => '40mm Jelly', 'price_per_unit' => 55],
                ['material_name' => 'M-Sand', 'price_per_unit' => 38],
                ['material_name' => 'P-Sand', 'price_per_unit' => 42],
                ['material_name' => 'Dust', 'price_per_unit' => 25],
                ['material_name' => '6mm Jelly', 'price_per_unit' => 48],
            ];

            $items = collect();

            foreach ($materials as $material) {
                $items->push(
                    Item::factory()->create([
                        'material_name' => $material['material_name'],
                        'price_per_unit' => $material['price_per_unit'],
                        'unit' => 'CFT',
                    ])
                );
            }

            /*
            |--------------------------------------------------------------------------
            | PARTIES
            |--------------------------------------------------------------------------
            */

            $customers = Party::factory()->count(20)
                ->create([
                    'party_type' => 'Customer',
                ]);

            $suppliers = Party::factory()->count(8)
                ->create([
                    'party_type' => 'Supplier',
                ]);

            $allParties = $customers->merge($suppliers);

            /*
            |--------------------------------------------------------------------------
            | VEHICLES + DRIVERS
            |--------------------------------------------------------------------------
            */

            $vehicles = collect();
            $drivers = collect();

            foreach ($suppliers->all() as $supplier) {

                $supplierVehicles = Vehicle::factory()->count(rand(2, 5))
                    ->create([
                        'party_id' => $supplier->id,
                    ]);

                foreach ($supplierVehicles as $vehicle) {

                    $vehicles->push($vehicle);

                    $driver = Driver::factory()->create([
                        'party_id' => $supplier->id,
                    ]);

                    $drivers->push($driver);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | CHALLANS
            |--------------------------------------------------------------------------
            */

            $challans = collect();

            for ($i = 1; $i <= 150; $i++) {

                $vehicle = $vehicles->random();

                $driver = $drivers->random();

                $item = $items->random();

                $customer = $customers->random();

                $quantity = rand(80, 450);

                $challan = Challan::factory()->create([
                    'challan_number' => 'CH-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                    'party_id' => $customer->id,
                    'vehicle_id' => $vehicle->id,
                    'driver_id' => $driver->id,
                    'item_id' => $item->id,
                    'quantity_cft' => $quantity,
                    'status' => 'Pending',
                    'created_at' => $faker->dateTimeBetween('-3 months', 'now'),
                ]);

                $challans->push($challan);
            }

            /*
            |--------------------------------------------------------------------------
            | INVOICES
            |--------------------------------------------------------------------------
            */

            $invoiceCounter = 1;

            foreach ($customers->all() as $customer) {

                $customerChallans = Challan::where('party_id', $customer->id)
                    ->inRandomOrder()
                    ->get();

                if ($customerChallans->count() === 0) {
                    continue;
                }

                $chunks = $customerChallans->chunk(rand(3, 8));

                foreach ($chunks as $chunk) {

                    $totalAmount = 0;

                    foreach ($chunk as $challan) {

                        $item = $items->firstWhere('id', $challan->item_id);

                        $totalAmount += (
                            $challan->quantity_cft *
                            $item->price_per_unit
                        );
                    }

                    $invoice = Invoice::factory()->create([
                        'invoice_number' => 'INV-' . str_pad($invoiceCounter++, 5, '0', STR_PAD_LEFT),
                        'party_id' => $customer->id,
                        'total_amount' => $totalAmount,
                        'driver_bata' => rand(500, 3000),
                        'payment_mode' => $faker->randomElement([
                            'Cash',
                            'UPI',
                            'Bank Transfer',
                            'Credit'
                        ]),
                        'created_at' => $faker->dateTimeBetween('-3 months', 'now'),
                    ]);

                    foreach ($chunk as $challan) {

                        $challan->update([
                            'invoice_id' => $invoice->id,
                            'status' => 'Invoiced',
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | LEDGER ENTRY - INVOICE
                    |--------------------------------------------------------------------------
                    */

                    $previousBalance = LedgerEntry::where(
                        'party_id',
                        $customer->id
                    )->latest('id')->value('balance') ?? 0;

                    $newBalance = $previousBalance + $invoice->total_amount;

                    LedgerEntry::factory()->create([
                        'entry_date' => $invoice->created_at,
                        'party_id' => $customer->id,

                        'recordable_type' => Invoice::class,
                        'recordable_id' => $invoice->id,

                        'description' => 'Invoice ' . $invoice->invoice_number,
                        'debit' => $invoice->total_amount,
                        'credit' => 0,
                        'balance' => $newBalance,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | PAYMENT VOUCHERS
                    |--------------------------------------------------------------------------
                    */

                    if (rand(0, 1)) {

                        $paidAmount = rand(
                            (int)($invoice->total_amount * 0.4),
                            (int)$invoice->total_amount
                        );

                        $voucher = Voucher::factory()->create([
                            'voucher_no' => 'RCPT-' . str_pad($invoice->id, 5, '0', STR_PAD_LEFT),
                            'voucher_date' => now()->subDays(rand(0, 30)),
                            'party_id' => $customer->id,
                            'voucher_type' => 'Receipt',
                            'amount' => $paidAmount,
                            'payment_mode' => $faker->randomElement([
                                'Cash',
                                'UPI',
                                'Bank Transfer',
                            ]),
                            'remarks' => 'Payment received',
                            'reference_invoice_id' => $invoice->id,
                        ]);

                        $updatedBalance = $newBalance - $paidAmount;

                        LedgerEntry::factory()->create([
                            'entry_date' => $voucher->voucher_date,
                            'party_id' => $customer->id,

                            'recordable_type' => Voucher::class,
                            'recordable_id' => $voucher->id,

                            'description' => 'Receipt against ' . $invoice->invoice_number,
                            'debit' => 0,
                            'credit' => $paidAmount,
                            'balance' => $updatedBalance,
                        ]);
                    }
                }
            }
        });
    }
}
