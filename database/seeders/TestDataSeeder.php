<?php

namespace Database\Seeders;

use App\Domains\Accounting\Models\Expense;
use App\Domains\Accounting\Models\Voucher;
use App\Domains\Master\Models\Driver;
use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\Party;
use App\Domains\Master\Models\StockLevel;
use App\Domains\Master\Models\Vehicle;
use App\Domains\Master\Models\Warehouse;
use App\Domains\Operations\Models\Challan;
use App\Domains\Operations\Models\Invoice;
use App\Domains\Operations\Models\ProductionEntry;
use App\Domains\Operations\Models\StockMovement;
use App\Domains\Operations\Models\StockReservation;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $customerParty = Party::create([
            'full_name' => 'Kumar Construction',
            'address_line_1' => '12 Industrial Layout',
            'address_line_2' => 'Near Lakeside Junction',
            'city' => 'Bengaluru',
            'state' => 'KA',
            'postal_code' => '560076',
            'contact_number' => '9448123456',
            'party_type' => 'Customer',
        ]);

        $supplierParty = Party::create([
            'full_name' => 'Sri Transport Services',
            'address_line_1' => '45 Logistics Road',
            'address_line_2' => 'HOSUR Main Road',
            'city' => 'Bengaluru',
            'state' => 'KA',
            'postal_code' => '560100',
            'contact_number' => '9845012345',
            'party_type' => 'Supplier',
        ]);

        $warehouseA = Warehouse::create([
            'name' => 'Main Stock Yard',
            'code' => 'MAIN',
            'is_active' => true,
        ]);

        $warehouseB = Warehouse::create([
            'name' => 'Secondary Yard',
            'code' => 'SECOND',
            'is_active' => true,
        ]);

        $item20mm = Item::create([
            'material_name' => '20mm Gravel',
            'price_per_unit' => 1250.00,
            'unit' => 'CFT',
        ]);

        $item40mm = Item::create([
            'material_name' => '40mm Gravel',
            'price_per_unit' => 1425.00,
            'unit' => 'CFT',
        ]);

        $itemMSand = Item::create([
            'material_name' => 'M-Sand',
            'price_per_unit' => 950.00,
            'unit' => 'CFT',
        ]);

        StockLevel::create([
            'item_id' => $item20mm->id,
            'warehouse_id' => $warehouseA->id,
            'available_qty' => 120.00,
            'reserved_qty' => 10.00,

        ]);

        StockLevel::create([
            'item_id' => $item40mm->id,
            'warehouse_id' => $warehouseA->id,
            'available_qty' => 90.00,
            'reserved_qty' => 5.00,
        ]);

        StockLevel::create([
            'item_id' => $itemMSand->id,
            'warehouse_id' => $warehouseB->id,
            'available_qty' => 65.00,
            'reserved_qty' => 8.00,
        ]);

        $vehicle = Vehicle::create([
            'party_id' => $supplierParty->id,
            'vehicle_number' => 'KA01AB1234',
            'capacity_cft' => 18.50,
            'unit' => 'CFT',
            'vehicle_type' => 'Tata 2518',
        ]);

        $driver = Driver::create([
            'party_id' => $supplierParty->id,
            'full_name' => 'Ramesh Kumar',
            'phone_number' => '9900876543',
        ]);

        $challan = Challan::create([
            'challan_number' => 'CH-2026-001',
            'party_id' => $customerParty->id,
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'item_id' => $item20mm->id,
            'invoice_id' => null,
            'quantity_cft' => 22.75,
            'payment_mode' => 'A/C',
            'status' => 'Pending',
        ]);

        $productionEntry = ProductionEntry::create([
            'production_entry_date' => now()->subDays(2)->toDateString(),
            'item_id' => $itemMSand->id,
            'warehouse_id' => $warehouseB->id,
            'quantity' => 45.00,
            'batch_no' => 'BATCH-001',
        ]);

        StockMovement::create([
            'item_id' => $item20mm->id,
            'warehouse_id' => $warehouseA->id,
            'challan_id' => $challan->id,
            'invoice_id' => null,
            'adjustment_id' => null,
            'movement_type' => 'OUT',
            'quantity' => 22.75,
            'unit_cost' => $item20mm->price_per_unit,
            'notes' => 'Shipment booked against challan '.$challan->challan_number,
        ]);

        StockReservation::create([
            'challan_id' => $challan->id,
            'warehouse_id' => $warehouseA->id,
            'item_id' => $item20mm->id,
            'quantity_reserved' => 22.75,
            'status' => 'finalized',
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-2026-001',
            'party_id' => $customerParty->id,
            'total_amount' => 22.75 * $item20mm->price_per_unit + 450.00,
            'driver_bata' => 450.00,
            'payment_mode' => 'Credit',
        ]);

        $challan->update([
            'invoice_id' => $invoice->id,
            'status' => 'Invoiced',
        ]);

        $voucher = Voucher::create([
            'voucher_no' => 'RC-2026-001',
            'voucher_date' => now()->toDateString(),
            'party_id' => $customerParty->id,
            'voucher_type' => 'Receipt',
            'amount' => $invoice->total_amount,
            'payment_mode' => 'Bank Transfer',
            'remarks' => 'Full payment received for invoice '.$invoice->invoice_number,
            'reference_invoice_id' => $invoice->id,
        ]);

        Expense::create([
            'expenditure_date' => now()->subDay()->toDateString(),
            'category' => 'Diesel',
            'amount' => 3120.00,
            'reference_no' => 'EXP-2026-001',
            'notes' => 'Diesel expense for delivery run',
        ]);
    }
}
