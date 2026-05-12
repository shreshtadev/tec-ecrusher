<?php

use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\StockLevel;
use App\Domains\Master\Models\Warehouse;
use App\Domains\Operations\Models\Challan;
use App\Domains\Operations\Models\Invoice;
use App\Domains\Operations\Models\StockMovement;
use App\Domains\Operations\Models\StockReservation;
use App\Domains\Operations\Services\StockService;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    // Disable event listeners to test the service in isolation
    Event::fake();

    // Create test data
    $this->warehouse = Warehouse::factory()->create(['is_active' => true]);
    $this->item = Item::factory()->create();

    // Set up stock level with initial inventory
    $this->stockLevel = StockLevel::create([
        'item_id' => $this->item->id,
        'warehouse_id' => $this->warehouse->id,
        'available_qty' => 100,
        'reserved_qty' => 0,
        'valuation_method' => 'FIFO',
    ]);

    $this->stockService = app(StockService::class);
});

test('can reserve stock for a challan', function () {
    // Create a challan (without triggering listeners since we faked events)
    $challan = Challan::factory()
        ->for($this->item, 'item')
        ->create(['quantity_cft' => 25]);

    // Directly call the service to reserve stock
    $reservation = $this->stockService->reserve($challan, $this->warehouse);

    // Assert that stock reservation was created
    expect($reservation)->not()->toBeNull();
    expect($reservation->quantity_reserved)->toBe(25);
    expect($reservation->status)->toBe('reserved');
    expect($reservation->challan_id)->toBe($challan->id);
    expect($reservation->warehouse_id)->toBe($this->warehouse->id);

    // Assert that stock level was updated
    $updatedStockLevel = $this->stockLevel->fresh();
    expect($updatedStockLevel->reserved_qty)->toBe(25);
    expect($updatedStockLevel->available_qty)->toBe(100); // Still 100 since we only reserved

    // Assert that RESERVE movement was created
    $movement = StockMovement::where('challan_id', $challan->id)
        ->where('movement_type', 'RESERVE')
        ->first();
    expect($movement)->not()->toBeNull();
    expect($movement->quantity)->toBe(25);
});

test('cannot reserve stock when insufficient quantity', function () {
    // Create a challan with quantity exceeding available stock
    $challan = Challan::factory()
        ->for($this->item, 'item')
        ->create(['quantity_cft' => 150]); // More than available (100)

    // This should throw an exception when trying to reserve
    expect(function () use ($challan) {
        $this->stockService->reserve($challan, $this->warehouse);
    })->toThrow(Exception::class);
});

test('can finalize stock when invoice is created', function () {
    // Step 1: Create a challan and reserve stock
    $challan = Challan::factory()
        ->for($this->item, 'item')
        ->create(['quantity_cft' => 25]);

    $this->stockService->reserve($challan, $this->warehouse);

    // Step 2: Create an invoice linked to the challan
    $invoice = Invoice::factory()->create();
    $challan->update(['invoice_id' => $invoice->id]);

    // Step 3: Finalize the stock (should convert RESERVE to OUT movement)
    $this->stockService->finalize($invoice);

    // Assert that OUT movement was created
    $outMovement = StockMovement::where('invoice_id', $invoice->id)
        ->where('movement_type', 'OUT')
        ->first();
    expect($outMovement)->not()->toBeNull();
    expect($outMovement->quantity)->toBe(-25); // Negative for outbound

    // Assert that reservation was marked as finalized
    $reservation = StockReservation::where('challan_id', $challan->id)->first();
    expect($reservation->status)->toBe('finalized');

    // Assert that stock level was updated
    $updatedStockLevel = $this->stockLevel->fresh();
    expect($updatedStockLevel->available_qty)->toBe(75); // 100 - 25
    expect($updatedStockLevel->reserved_qty)->toBe(0); // Moved from reserved to finalized

test('complete workflow: reserve to finalize', function () {
    // Step 1: Create challan and verify stock is reserved
    $challan = Challan::factory()
        ->for($this->item, 'item')
        ->create(['quantity_cft' => 30]);

    $this->stockService->reserve($challan, $this->warehouse);

    $this->stockLevel->refresh();
    expect($this->stockLevel->available_qty)->toBe(100); // Still 100
    expect($this->stockLevel->reserved_qty)->toBe(30);

    // Step 2: Create invoice to finalize the shipment
    $invoice = Invoice::factory()->create();
    $challan->update(['invoice_id' => $invoice->id]);

    $this->stockService->finalize($invoice);

    // Step 3: Verify stock moved from reserved to finalized
    $this->stockLevel->refresh();
    expect($this->stockLevel->available_qty)->toBe(70); // Now reduced by 30
    expect($this->stockLevel->reserved_qty)->toBe(0);  // All reserved moved to OUT

    // Step 4: Verify all movements are recorded
    $movements = StockMovement::where('item_id', $this->item->id)->get();
    expect($movements)->toHaveCount(2); // RESERVE + OUT
    expect($movements->where('movement_type', 'RESERVE')->count())->toBe(1);
    expect($movements->where('movement_type', 'OUT')->count())->toBe(1);
});

test('can unreserve stock when challan is cancelled', function () {
    // Step 1: Create and reserve stock for challan
    $challan = Challan::factory()
        ->for($this->item, 'item')
        ->create(['quantity_cft' => 25]);

    $this->stockService->reserve($challan, $this->warehouse);

    $this->stockLevel->refresh();
    expect($this->stockLevel->reserved_qty)->toBe(25);

    // Step 2: Unreserve the stock (simulate challan cancellation)
    $this->stockService->unreserve($challan);

    // Assert reservation is cancelled
    $reservation = StockReservation::where('challan_id', $challan->id)->first();
    expect($reservation->status)->toBe('cancelled');

    // Assert stock level is restored
    $this->stockLevel->refresh();
    expect($this->stockLevel->reserved_qty)->toBe(0);
    expect($this->stockLevel->available_qty)->toBe(100); // Back to original

    // Assert UNRESERVE movement is created
    $movement = StockMovement::where('challan_id', $challan->id)
        ->where('movement_type', 'UNRESERVE')
        ->first();
    expect($movement)->not()->toBeNull();
    expect($movement->quantity)->toBe(-25);
});
