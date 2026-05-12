<?php

use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\StockLevel;
use App\Domains\Master\Models\Warehouse;
use App\Domains\Operations\Events\ChallanFinalized;
use App\Domains\Operations\Models\Challan;
use App\Domains\Operations\Models\Invoice;
use App\Domains\Operations\Models\StockMovement;

it('reserves stock when a challan is created', function () {
    $warehouse = Warehouse::create([
        'name' => 'Main Warehouse',
        'code' => 'WH-001',
        'is_active' => true,
    ]);
    $item = Item::factory()->create();

    StockLevel::factory()->create([
        'item_id' => $item->id,
        'warehouse_id' => $warehouse->id,
        'available_qty' => 100,
        'reserved_qty' => 0,
    ]);

    $challan = Challan::factory()->create([
        'item_id' => $item->id,
        'quantity_cft' => 25,
        'status' => 'Pending',
        'payment_mode' => 'Cash',
    ]);

    $this->assertDatabaseHas('stock_reservations', [
        'challan_id' => $challan->id,
        'item_id' => $item->id,
        'warehouse_id' => $warehouse->id,
        'quantity_reserved' => 25,
        'status' => 'reserved',
    ]);

    $reservedQty = StockLevel::where('item_id', $item->id)
        ->where('warehouse_id', $warehouse->id)
        ->value('reserved_qty');

    expect($reservedQty)->toBe(25.0);
});

it('creates an invoice and finalizes stock when a challan is finalized', function () {
    $warehouse = Warehouse::create([
        'name' => 'Main Warehouse',
        'code' => 'WH-001',
        'is_active' => true,
    ]);
    $item = Item::factory()->create(['price_per_unit' => 100]);
    StockLevel::factory()->create([
        'item_id' => $item->id,
        'warehouse_id' => $warehouse->id,
        'available_qty' => 200,
        'reserved_qty' => 0,
    ]);

    $challan = Challan::factory()->create([
        'item_id' => $item->id,
        'quantity_cft' => 50,
        'status' => 'Pending',
        'payment_mode' => 'Cash',
    ]);

    expect($challan->stockReservation)->not->toBeNull();

    ChallanFinalized::dispatch($challan);

    $challan->refresh();
    $invoice = Invoice::where('id', $challan->invoice_id)->first();

    expect($challan->status)->toBe('Invoiced');
    expect($invoice)->not->toBeNull();
    expect($invoice->party_id)->toBe($challan->party_id);
    expect($invoice->total_amount)->toBe(5000.0);
    expect($invoice->payment_mode)->toBe('Cash');

    $stockLevel = StockLevel::where('item_id', $item->id)
        ->where('warehouse_id', $warehouse->id)
        ->first();

    expect($stockLevel->available_qty)->toBe(150.0);
    expect($stockLevel->reserved_qty)->toBe(0.0);

    expect(StockMovement::where('challan_id', $challan->id)
        ->where('movement_type', 'RESERVE')
        ->exists())->toBeTrue();

    expect(StockMovement::where('invoice_id', $invoice->id)
        ->where('movement_type', 'OUT')
        ->exists())->toBeTrue();
});
