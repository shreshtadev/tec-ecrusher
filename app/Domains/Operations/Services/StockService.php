<?php

namespace App\Domains\Operations\Services;

use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\StockLevel;
use App\Domains\Master\Models\Warehouse;
use App\Domains\Operations\Models\Challan;
use App\Domains\Operations\Models\Invoice;
use App\Domains\Operations\Models\StockMovement;
use App\Domains\Operations\Models\StockReservation;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class StockService
{
    /**
     * Reserve stock for a challan.
     *
     * @throws Exception if stock is insufficient
     */
    public function reserve(Challan $challan, ?Warehouse $warehouse = null): StockReservation
    {
        if (! $warehouse) {
            $warehouse = Warehouse::where('is_active', true)->first();
            if (! $warehouse) {
                throw new Exception('No active warehouse found');
            }
        }

        // Check if stock is available
        if (! $this->validateReservation($challan->item, $warehouse, $challan->quantity_cft)) {
            throw new Exception("Insufficient stock for {$challan->item->material_name}. Available: {$this->getAvailableStock($challan->item,$warehouse)} CFT");
        }

        // Create stock reservation
        $reservation = StockReservation::create([
            'challan_id' => $challan->id,
            'warehouse_id' => $warehouse->id,
            'item_id' => $challan->item_id,
            'quantity_reserved' => $challan->quantity_cft,
            'status' => 'reserved',
        ]);

        // Create RESERVE movement
        StockMovement::create([
            'item_id' => $challan->item_id,
            'warehouse_id' => $warehouse->id,
            'challan_id' => $challan->id,
            'movement_type' => 'RESERVE',
            'quantity' => $challan->quantity_cft,
            'unit_cost' => $challan->item->price_per_unit,
            'notes' => "Reserved for Challan #{$challan->challan_number}",
        ]);

        // Update stock level reserved quantity
        $stockLevel = StockLevel::where('item_id', $challan->item_id)
            ->where('warehouse_id', $warehouse->id)
            ->first();

        if ($stockLevel) {
            $stockLevel->increment('reserved_qty', $challan->quantity_cft);
        }

        return $reservation;
    }

    /**
     * Finalize stock reservation when invoice is created.
     * Convert RESERVE movements to OUT movements.
     */
    public function finalize(Invoice $invoice): void
    {
        $challans = $invoice->challans;

        foreach ($challans as $challan) {
            $reservation = $challan->stockReservation;

            if (! $reservation || $reservation->status === 'finalized') {
                continue;
            }

            $warehouse = $reservation->warehouse;
            $item = $reservation->item;
            $quantity = $reservation->quantity_reserved;

            // Create OUT movement
            StockMovement::create([
                'item_id' => $item->id,
                'warehouse_id' => $warehouse->id,
                'invoice_id' => $invoice->id,
                'movement_type' => 'OUT',
                'quantity' => -$quantity, // Negative for outbound
                'unit_cost' => $item->price_per_unit,
                'notes' => "Finalized from Invoice #{$invoice->invoice_number}",
            ]);

            // Update stock level
            $stockLevel = StockLevel::where('item_id', $item->id)
                ->where('warehouse_id', $warehouse->id)
                ->first();

            if ($stockLevel) {
                $stockLevel->decrement('available_qty', $quantity);
                $stockLevel->decrement('reserved_qty', $quantity);
            }

            // Mark reservation as finalized
            $reservation->update(['status' => 'finalized']);
        }
    }

    /**
     * Unreserve stock when challan is cancelled.
     */
    public function unreserve(Challan $challan): void
    {
        $reservation = $challan->stockReservation;

        if (! $reservation || $reservation->status !== 'reserved') {
            return;
        }

        $warehouse = $reservation->warehouse;
        $item = $reservation->item;
        $quantity = $reservation->quantity_reserved;

        // Create UNRESERVE movement
        StockMovement::create([
            'item_id' => $item->id,
            'warehouse_id' => $warehouse->id,
            'challan_id' => $challan->id,
            'movement_type' => 'UNRESERVE',
            'quantity' => -$quantity,
            'notes' => "Unreserved from Challan #{$challan->challan_number}",
        ]);

        // Update stock level
        $stockLevel = StockLevel::where('item_id', $item->id)
            ->where('warehouse_id', $warehouse->id)
            ->first();

        if ($stockLevel) {
            $stockLevel->decrement('reserved_qty', $quantity);
        }

        // Mark reservation as cancelled
        $reservation->update(['status' => 'cancelled']);
    }

    /**
     * Handle return of goods from a challan.
     */
    public function handleReturn(Challan $challan, float $returnQuantity, ?Warehouse $warehouse = null): void
    {
        if (! $warehouse) {
            $warehouse = Warehouse::where('is_active', true)->first();
        }

        $reservation = $challan->stockReservation;
        if (! $reservation) {
            throw new Exception('No reservation found for challan');
        }

        // Create IN movement (reverse of OUT)
        StockMovement::create([
            'item_id' => $challan->item_id,
            'warehouse_id' => $warehouse->id,
            'challan_id' => $challan->id,
            'movement_type' => 'IN',
            'quantity' => $returnQuantity,
            'unit_cost' => $challan->item->price_per_unit,
            'notes' => "Return from Challan #{$challan->challan_number}",
        ]);

        // Update stock level
        $stockLevel = StockLevel::where('item_id', $challan->item_id)
            ->where('warehouse_id', $warehouse->id)
            ->first();

        if ($stockLevel) {
            $stockLevel->increment('available_qty', $returnQuantity);
        }
    }

    /**
     * Get available stock (available_qty - reserved_qty).
     */
    public function getAvailableStock(Item $item, Warehouse $warehouse): float
    {
        $stockLevel = StockLevel::where('item_id', $item->id)
            ->where('warehouse_id', $warehouse->id)
            ->first();

        if (! $stockLevel) {
            return 0;
        }

        return max(0, $stockLevel->available_qty - $stockLevel->reserved_qty);
    }

    /**
     * Validate if sufficient stock is available for reservation.
     */
    public function validateReservation(Item $item, Warehouse $warehouse, float $quantity): bool
    {
        return $this->getAvailableStock($item, $warehouse) >= $quantity;
    }

    /**
     * Get items below low stock threshold.
     */
    public function getLowStockItems(?Warehouse $warehouse = null): Collection
    {
        $threshold = config('inventory.low_stock_threshold_percent', 10) / 100;

        $query = StockLevel::with(['item', 'warehouse']);

        if ($warehouse) {
            $query->where('warehouse_id', $warehouse->id);
        }

        $items = $query->get();

        // Filter items below threshold
        return $items->filter(function ($stockLevel) use ($threshold) {
            // Calculate average usage if needed; for now, use a simple threshold
            return $stockLevel->available_qty < ($stockLevel->available_qty + $stockLevel->reserved_qty) * $threshold;
        });
    }

    /**
     * Get stock valuation using FIFO or LIFO method.
     */
    public function getStockValuation(Item $item, Warehouse $warehouse, string $method = 'FIFO'): array
    {
        $stockLevel = StockLevel::where('item_id', $item->id)
            ->where('warehouse_id', $warehouse->id)
            ->first();

        if (! $stockLevel) {
            return ['quantity' => 0, 'total_cost' => 0, 'average_cost' => 0];
        }

        $movements = StockMovement::where('item_id', $item->id)
            ->where('warehouse_id', $warehouse->id)
            ->whereIn('movement_type', ['IN', 'ADJUSTMENT'])
            ->orderBy('created_at', $method === 'FIFO' ? 'asc' : 'desc')
            ->get();

        $totalCost = 0;
        $remainingQty = $stockLevel->available_qty;

        foreach ($movements as $movement) {
            if ($remainingQty <= 0) {
                break;
            }

            $qtyToValue = min($movement->quantity, $remainingQty);
            $totalCost += $qtyToValue * ($movement->unit_cost ?? $item->price_per_unit);
            $remainingQty -= $qtyToValue;
        }

        return [
            'quantity' => $stockLevel->available_qty,
            'total_cost' => $totalCost,
            'average_cost' => $stockLevel->available_qty > 0 ? $totalCost / $stockLevel->available_qty : 0,
        ];
    }

    /**
     * Initialize stock level for an item in a warehouse.
     */
    public function initializeStockLevel(Item $item, Warehouse $warehouse, float $initialQuantity = 0, string $valuationMethod = 'FIFO'): StockLevel
    {
        return StockLevel::firstOrCreate(
            [
                'item_id' => $item->id,
                'warehouse_id' => $warehouse->id,
            ],
            [
                'available_qty' => $initialQuantity,
                'reserved_qty' => 0,
                'valuation_method' => $valuationMethod,
            ]
        );
    }

    /**
     * Inbound stock receipt (stock IN).
     */
    public function receiveStock(Item $item, Warehouse $warehouse, float $quantity, float $unitCost, ?string $reference = null): StockMovement
    {
        // Initialize stock level if not exists
        $stockLevel = $this->initializeStockLevel($item, $warehouse);

        // Create IN movement
        $movement = StockMovement::create([
            'item_id' => $item->id,
            'warehouse_id' => $warehouse->id,
            'movement_type' => 'IN',
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'notes' => $reference ? "Stock received - Reference: {$reference}" : 'Stock received',
        ]);

        // Update stock level
        $stockLevel->increment('available_qty', $quantity);

        return $movement;
    }
}
