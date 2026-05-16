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
     * Get items below low stock threshold with advanced filtering.
     *
     * @param  float  $thresholdPercent  Custom threshold percentage (0-100)
     * @param  int  $limit  Maximum results to return
     */
    public function getLowStockItems(?Warehouse $warehouse = null, ?float $thresholdPercent = null, int $limit = 50): Collection
    {
        $threshold = ($thresholdPercent ?? config('inventory.low_stock_threshold_percent', 10)) / 100;

        $query = StockLevel::with(['item:id,material_name,price_per_unit', 'warehouse:id,name,code'])
            ->select('id', 'item_id', 'warehouse_id', 'available_qty', 'reserved_qty', 'valuation_method')
            ->where('available_qty', '>', 0)
            ->orderBy('available_qty', 'asc')
            ->limit($limit);

        if ($warehouse) {
            $query->where('warehouse_id', $warehouse->id);
        }

        $items = $query->get();

        // Filter items below threshold - those with low available stock relative to total
        return $items->filter(function ($stockLevel) use ($threshold) {
            $totalStock = $stockLevel->available_qty + $stockLevel->reserved_qty;
            if ($totalStock === 0) {
                return false;
            }
            $availablePercent = $stockLevel->available_qty / $totalStock;

            return $availablePercent < $threshold;
        })->values();
    }

    /**
     * Get critical stock alerts with detailed information.
     *
     * Items where available stock is at or below zero after reservations.
     */
    public function getCriticalStockItems(?Warehouse $warehouse = null): Collection
    {
        $query = StockLevel::with(['item:id,material_name,price_per_unit', 'warehouse:id,name,code'])
            ->select('id', 'item_id', 'warehouse_id', 'available_qty', 'reserved_qty')
            ->whereRaw('(available_qty - reserved_qty) <= 0');

        if ($warehouse) {
            $query->where('warehouse_id', $warehouse->id);
        }

        return $query->get();
    }

    /**
     * Get stock valuation using FIFO, LIFO, or Weighted Average Cost method.
     *
     * Returns detailed valuation including current cost, total value, and per-unit cost.
     */
    public function getStockValuation(Item $item, Warehouse $warehouse, string $method = 'FIFO'): array
    {
        $stockLevel = StockLevel::where('item_id', $item->id)
            ->where('warehouse_id', $warehouse->id)
            ->select('id', 'available_qty', 'reserved_qty', 'valuation_method')
            ->first();

        if (! $stockLevel || $stockLevel->available_qty <= 0) {
            return [
                'item_id' => $item->id,
                'item_name' => $item->material_name,
                'warehouse_id' => $warehouse->id,
                'quantity' => 0,
                'total_cost' => 0,
                'average_cost' => 0,
                'method' => $method,
                'inventory_value' => 0,
            ];
        }

        // Use the valuation method stored in stock level if not overridden
        $valuationMethod = $method === 'FIFO' || $method === 'LIFO' ? $method : $stockLevel->valuation_method;

        if ($valuationMethod === 'WEIGHTED_AVERAGE') {
            $valuation = $this->calculateWeightedAverageCost($item->id, $warehouse->id, $stockLevel->available_qty);
        } else {
            $valuation = $this->calculateFIFOLIFOValuation(
                $item->id,
                $warehouse->id,
                $stockLevel->available_qty,
                $valuationMethod
            );
        }

        $inventoryValue = $valuation['total_cost'];

        return [
            'item_id' => $item->id,
            'item_name' => $item->material_name,
            'warehouse_id' => $warehouse->id,
            'quantity' => $stockLevel->available_qty,
            'reserved_qty' => $stockLevel->reserved_qty,
            'available_qty' => $this->getAvailableStock($item, $warehouse),
            'total_cost' => $valuation['total_cost'],
            'average_cost' => $valuation['average_cost'],
            'inventory_value' => $inventoryValue,
            'method' => $valuationMethod,
        ];
    }

    /**
     * Calculate FIFO/LIFO valuation.
     */
    private function calculateFIFOLIFOValuation(int $itemId, int $warehouseId, float $quantity, string $method): array
    {
        $movements = StockMovement::where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->whereIn('movement_type', ['IN', 'ADJUSTMENT'])
            ->select('id', 'quantity', 'unit_cost', 'created_at')
            ->orderBy('created_at', $method === 'FIFO' ? 'asc' : 'desc')
            ->get();

        $totalCost = 0;
        $remainingQty = $quantity;

        foreach ($movements as $movement) {
            if ($remainingQty <= 0) {
                break;
            }

            $qtyToValue = min($movement->quantity, $remainingQty);
            $totalCost += $qtyToValue * ($movement->unit_cost ?? 0);
            $remainingQty -= $qtyToValue;
        }

        return [
            'total_cost' => $totalCost,
            'average_cost' => $quantity > 0 ? $totalCost / $quantity : 0,
        ];
    }

    /**
     * Calculate Weighted Average Cost valuation.
     */
    private function calculateWeightedAverageCost(int $itemId, int $warehouseId, float $currentQuantity): array
    {
        // Get all movements to calculate average cost
        $movements = StockMovement::where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->whereIn('movement_type', ['IN', 'ADJUSTMENT'])
            ->select('quantity', 'unit_cost')
            ->get();

        $totalQuantity = $movements->sum('quantity');
        $totalCost = $movements->reduce(function ($carry, $movement) {
            return $carry + ($movement->quantity * ($movement->unit_cost ?? 0));
        }, 0);

        $weightedAverageCost = $totalQuantity > 0 ? $totalCost / $totalQuantity : 0;

        return [
            'total_cost' => $currentQuantity * $weightedAverageCost,
            'average_cost' => $weightedAverageCost,
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

    /**
     * Receive stock in batch for multiple items.
     *
     * @param  array  $items  Array of ['item_id' => id, 'warehouse_id' => id, 'quantity' => qty, 'unit_cost' => cost, 'reference' => ref]
     * @return Collection of created movements
     */
    public function receiveStockBatch(array $items, ?string $batchReference = null): Collection
    {
        $movements = collect();

        foreach ($items as $itemData) {
            try {
                $item = Item::find($itemData['item_id']);
                $warehouse = Warehouse::find($itemData['warehouse_id']);

                if (! $item || ! $warehouse) {
                    continue;
                }

                $itemRef = $itemData['reference'] ?? '';
                $reference = $batchReference ? "{$batchReference} - {$itemRef}" : ($itemRef ?: null);
                $movement = $this->receiveStock($item, $warehouse, $itemData['quantity'], $itemData['unit_cost'], $reference);
                $movements->push($movement);
            } catch (Exception $e) {
                report($e);

                continue;
            }
        }

        return $movements;
    }

    /**
     * Get stock movement history for an item/warehouse.
     *
     * Useful for auditing and stock reconciliation.
     */
    public function getStockMovementHistory(Item $item, Warehouse $warehouse, int $days = 30): Collection
    {
        $fromDate = now()->subDays($days);

        return StockMovement::where('item_id', $item->id)
            ->where('warehouse_id', $warehouse->id)
            ->where('created_at', '>=', $fromDate)
            ->select(['id', 'movement_type', 'quantity', 'unit_cost', 'notes', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get complete inventory summary for a warehouse.
     *
     * Returns aggregated data for dashboard and reporting.
     */
    public function getInventorySummary(?Warehouse $warehouse = null): array
    {
        $query = StockLevel::with(['item:id,material_name,price_per_unit']);

        if ($warehouse) {
            $query->where('warehouse_id', $warehouse->id);
        } else {
            $query->select('id', 'item_id', 'warehouse_id', 'available_qty', 'reserved_qty', 'valuation_method');
        }

        $stocks = $query->get();

        $totalInventoryValue = 0;
        $totalAvailableQty = 0;
        $totalReservedQty = 0;
        $lowStockCount = 0;
        $criticalStockCount = 0;

        foreach ($stocks as $stock) {
            $totalAvailableQty += $stock->available_qty;
            $totalReservedQty += $stock->reserved_qty;

            // Calculate inventory value
            $valuation = $this->calculateWeightedAverageCost($stock->item_id, $stock->warehouse_id, $stock->available_qty);
            $totalInventoryValue += $valuation['total_cost'];

            // Count low stock items
            $availablePercent = ($stock->available_qty + $stock->reserved_qty) > 0
                ? $stock->available_qty / ($stock->available_qty + $stock->reserved_qty)
                : 0;

            if ($availablePercent < (config('inventory.low_stock_threshold_percent', 10) / 100)) {
                $lowStockCount++;
            }

            if (($stock->available_qty - $stock->reserved_qty) <= 0) {
                $criticalStockCount++;
            }
        }

        return [
            'total_items' => $stocks->count(),
            'total_available_qty' => $totalAvailableQty,
            'total_reserved_qty' => $totalReservedQty,
            'total_inventory_value' => $totalInventoryValue,
            'low_stock_items' => $lowStockCount,
            'critical_stock_items' => $criticalStockCount,
            'warehouse_id' => $warehouse?->id,
        ];
    }

    /**
     * Get detailed inventory value across all warehouses.
     *
     * Useful for financial reporting and balance sheet calculations.
     */
    public function getInventoryValue(?Item $item = null): array
    {
        $query = StockLevel::with(['item:id,material_name,price_per_unit', 'warehouse:id,name,code'])
            ->select('id', 'item_id', 'warehouse_id', 'available_qty', 'reserved_qty', 'valuation_method');

        if ($item) {
            $query->where('item_id', $item->id);
        }

        $stocks = $query->get();

        $valuations = [];
        $totalValue = 0;

        foreach ($stocks as $stock) {
            $valuation = $this->calculateWeightedAverageCost($stock->item_id, $stock->warehouse_id, $stock->available_qty);
            $itemValue = $valuation['total_cost'];
            $totalValue += $itemValue;

            $valuations[] = [
                'item_id' => $stock->item_id,
                'item_name' => $stock->item->material_name,
                'warehouse_id' => $stock->warehouse_id,
                'warehouse_name' => $stock->warehouse->name,
                'quantity' => $stock->available_qty,
                'unit_cost' => $valuation['average_cost'],
                'total_value' => $itemValue,
            ];
        }

        return [
            'valuations' => $valuations,
            'total_inventory_value' => $totalValue,
        ];
    }

    /**
     * Adjust stock for reconciliation or inventory corrections.
     *
     * Creates an ADJUSTMENT movement record.
     */
    public function adjustStock(Item $item, Warehouse $warehouse, float $quantityChange, float $reason, ?float $unitCost = null): StockMovement
    {
        $stockLevel = $this->initializeStockLevel($item, $warehouse);

        // Validate adjustment won't make stock negative
        if (($stockLevel->available_qty + $quantityChange) < 0) {
            throw new Exception("Cannot adjust stock below 0 for {$item->material_name}. Current: {$stockLevel->available_qty}");
        }

        $movement = StockMovement::create([
            'item_id' => $item->id,
            'warehouse_id' => $warehouse->id,
            'movement_type' => 'ADJUSTMENT',
            'quantity' => $quantityChange,
            'unit_cost' => $unitCost ?? $item->price_per_unit,
            'notes' => "Adjustment: {$reason}",
        ]);

        // Update stock level
        if ($quantityChange > 0) {
            $stockLevel->increment('available_qty', $quantityChange);
        } else {
            $stockLevel->decrement('available_qty', abs($quantityChange));
        }

        return $movement;
    }

    /**
     * Get stock reorder points based on average usage.
     *
     * Helps with purchase planning to maintain optimal inventory levels.
     */
    public function getReorderPoints(?Warehouse $warehouse = null, int $lookbackDays = 30): Collection
    {
        $cutoffDate = now()->subDays($lookbackDays);

        $query = StockMovement::where('movement_type', 'OUT')
            ->where('created_at', '>=', $cutoffDate)
            ->select('item_id', 'warehouse_id')
            ->selectRaw('ABS(SUM(quantity)) as total_outbound')
            ->selectRaw('COUNT(*) as transaction_count')
            ->groupBy('item_id', 'warehouse_id')
            ->orderBy('total_outbound', 'desc');

        if ($warehouse) {
            $query->where('warehouse_id', $warehouse->id);
        }

        return $query
            ->with(['item:id,material_name,price_per_unit', 'warehouse:id,name,code'])
            ->get()
            ->map(function ($record) use ($lookbackDays) {
                $dailyUsage = $record->total_outbound / $lookbackDays;
                // Reorder point = daily usage * lead time (assuming 7 days lead time) + safety stock (7 days)
                $reorderPoint = $dailyUsage * 14;

                return [
                    'item_id' => $record->item_id,
                    'item_name' => $record->item->material_name ?? 'N/A',
                    'warehouse_id' => $record->warehouse_id,
                    'warehouse_name' => $record->warehouse->name ?? 'N/A',
                    'daily_usage' => round($dailyUsage, 2),
                    'total_usage_period' => round($record->total_outbound, 2),
                    'reorder_point' => round($reorderPoint, 2),
                    'lookback_days' => $lookbackDays,
                ];
            });
    }
}
