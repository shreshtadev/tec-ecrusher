<?php

namespace Database\Seeders;

use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\StockLevel;
use App\Domains\Master\Models\Warehouse;
use App\Domains\Operations\Models\StockMovement;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default warehouse if not exists
        $warehouse = Warehouse::firstOrCreate(
            ['code' => 'WH-DEFAULT'],
            [
                'name' => 'Default Warehouse',
                'code' => 'WH-DEFAULT',
                'is_active' => true,
            ]
        );

        // Create test warehouses
        $warehouse2 = Warehouse::firstOrCreate(
            ['code' => 'WH-1'],
            [
                'name' => 'Warehouse 1',
                'code' => 'WH-1',
                'is_active' => true,
            ]
        );

        $warehouse3 = Warehouse::firstOrCreate(
            ['code' => 'WH-TEST-2'],
            [
                'name' => 'Test Warehouse 2',
                'code' => 'WH-TEST-2',
                'is_active' => false,
            ]
        );

        // Get existing items or create them
        $items = Item::all();

        if ($items->isEmpty()) {
            // Create some test items
            $items = [
                Item::create([
                    'material_name' => '20mm',
                    'price_per_unit' => 50.00,
                    'unit' => 'CFT',
                ]),
                Item::create([
                    'material_name' => '40mm',
                    'price_per_unit' => 60.00,
                    'unit' => 'CFT',
                ]),
                Item::create([
                    'material_name' => 'M-Sand',
                    'price_per_unit' => 40.00,
                    'unit' => 'CFT',
                ]),
            ];
        }

        // Initialize stock levels for each item in default warehouse
        foreach ($items as $item) {
            // Initialize stock level in default warehouse
            $stockLevel = StockLevel::firstOrCreate(
                [
                    'item_id' => $item->id,
                    'warehouse_id' => $warehouse->id,
                ],
                [
                    'available_qty' => 500,
                    'reserved_qty' => 0,
                    'valuation_method' => 'FIFO',
                ]
            );

            // Create initial IN movement
            StockMovement::firstOrCreate(
                [
                    'item_id' => $item->id,
                    'warehouse_id' => $warehouse->id,
                    'movement_type' => 'IN',
                    'notes' => 'Initial stock',
                ],
                [
                    'quantity' => 500,
                    'unit_cost' => $item->price_per_unit,
                ]
            );

            // Initialize stock level in test warehouse
            $stockLevel2 = StockLevel::firstOrCreate(
                [
                    'item_id' => $item->id,
                    'warehouse_id' => $warehouse2->id,
                ],
                [
                    'available_qty' => 250,
                    'reserved_qty' => 0,
                    'valuation_method' => 'FIFO',
                ]
            );

            StockMovement::firstOrCreate(
                [
                    'item_id' => $item->id,
                    'warehouse_id' => $warehouse2->id,
                    'movement_type' => 'IN',
                    'notes' => 'Initial stock',
                ],
                [
                    'quantity' => 250,
                    'unit_cost' => $item->price_per_unit,
                ]
            );
        }
    }
}
