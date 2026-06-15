<?php

namespace App\Services;

use App\Enums\ExpenseOpts;
use App\Enums\PaymentOpts;
use App\Models\Challan;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\ProductionEntry;
use App\Models\StockLevel;
use App\Models\StockMovement;
use App\Models\StockReservation;
use App\Models\Warehouse;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockService
{
    /**
     * Reserve stock for all items in a challan.
     *
     *
     * @throws Exception if stock is insufficient for any item
     */
    public function reserve(Challan $challan): void
    {
        $challan->load('challan_items.item');
        $challan->load('challan_items.warehouse');

        if ($challan->challan_items->isEmpty()) {
            throw new Exception('Challan has no items to reserve');
        }

        foreach ($challan->challan_items as $challanItem) {
            $warehouse = $challanItem->warehouse;
            $stockLevel = StockLevel::where('item_id', $challanItem->item_id)
                ->where('warehouse_id', $warehouse->id)
                ->lockForUpdate()
                ->first();

            $availableStock = $this->getAvailableStock($challanItem->item, $warehouse);
            if (! $stockLevel || $availableStock < $challanItem->quantity_cft) {
                throw new Exception("Insufficient stock for {$challanItem->item->material_name}. Available: {$availableStock} CFT");
            }

            $reservation = StockReservation::create([
                'source_type' => Challan::class,
                'source_id' => $challan->id,
                'warehouse_id' => $warehouse->id,
                'item_id' => $challanItem->item_id,
                'quantity' => $challanItem->quantity_cft,
                'status' => 'reserved',
                'reserved_at' => now(),
                'remarks' => "Reserved for Challan #{$challan->challan_number}",
            ]);

            $stockLevel->increment('reserved_qty', $challanItem->quantity_cft);
        }
    }

    /**
     * Finalize stock reservation when invoice is created.
     * Convert RESERVE movements to OUT movements for all challan items.
     */
    public function finalize(Invoice $invoice): void
    {
        foreach ($invoice->challans as $challan) {
            $challan->load('challan_items.item');

            foreach ($challan->challan_items as $challanItem) {
                $reservation = StockReservation::where('source_type', Challan::class)
                    ->where('source_id', $challan->id)
                    ->where('item_id', $challanItem->item_id)
                    ->where('status', 'reserved')
                    ->lockForUpdate()
                    ->first();

                if (! $reservation) {
                    continue;
                }

                $quantity = (float) $reservation->quantity;
                $stockLevel = StockLevel::where('item_id', $reservation->item_id)
                    ->where('warehouse_id', $reservation->warehouse_id)
                    ->lockForUpdate()
                    ->first();

                if (! $stockLevel || $stockLevel->available_qty < $quantity || $stockLevel->reserved_qty < $quantity) {
                    throw new Exception('Stock level mismatch while finalizing reservation.');
                }

                StockMovement::create([
                    'item_id' => $reservation->item_id,
                    'warehouse_id' => $reservation->warehouse_id,
                    'source_type' => Invoice::class,
                    'source_id' => $invoice->id,
                    'movement_type' => 'OUT',
                    'quantity' => $quantity,
                    'unit_cost' => $challanItem->rate_at_sale,
                    'movement_date' => now(),
                    'remarks' => "Finalized from Invoice #{$invoice->invoice_number}",
                ]);

                $stockLevel->decrement('available_qty', $quantity);
                $stockLevel->decrement('reserved_qty', $quantity);
                $reservation->update(['status' => 'finalized', 'finalized_at' => now()]);
            }
        }
    }

    /**
     * Unreserve stock for all items when challan is cancelled.
     */
    public function unreserve(Challan $challan): void
    {
        DB::transaction(function () use ($challan): void {
            $challan->load('challan_items');

            $reservations = StockReservation::where('source_type', Challan::class)
                ->where('source_id', $challan->id)
                ->where('status', 'reserved')
                ->lockForUpdate()
                ->get();

            if ($reservations->isEmpty()) {
                return;
            }

            foreach ($reservations as $reservation) {
                $stockLevel = StockLevel::where('item_id', $reservation->item_id)
                    ->where('warehouse_id', $reservation->warehouse_id)
                    ->lockForUpdate()
                    ->first();

                if ($stockLevel) {
                    $stockLevel->decrement('reserved_qty', min($stockLevel->reserved_qty, $reservation->quantity));
                }

                $reservation->update(['status' => 'cancelled', 'cancelled_at' => now()]);
            }
        });
    }

    /**
     * Handle return of goods from a challan item.
     * Creates an IN movement and updates stock levels accordingly.
     */
    public function handleReturn(Challan $challan, float $returnQuantity, ?Warehouse $warehouse = null): void
    {
        $warehouse ??= Warehouse::where('is_active', true)->first();
        if (! $warehouse) {
            throw new Exception('No active warehouse found');
        }

        $challan->load('challan_items.item');

        if ($challan->challan_items->isEmpty()) {
            throw new Exception('Challan has no items to return');
        }

        if ($challan->challan_items->count() > 1) {
            throw new Exception('Cannot return a challan with multiple items without a specific challan item id.');
        }

        $challanItem = $challan->challan_items->first();

        $reservation = StockReservation::where('source_type', Challan::class)
            ->where('source_id', $challan->id)
            ->where('item_id', $challanItem->item_id)
            ->first();

        if (! $reservation) {
            throw new Exception('No reservation found for this challan item');
        }

        // Create IN movement (reverse of OUT)
        StockMovement::create([
            'item_id' => $challanItem->item_id,
            'warehouse_id' => $warehouse->id,
            'source_type' => Challan::class,
            'source_id' => $challan->id,
            'movement_type' => 'IN',
            'quantity' => $returnQuantity,
            'unit_cost' => $challanItem->rate_at_sale,
            'movement_date' => now(),
            'remarks' => "Return from Challan #{$challan->challan_number}",
        ]);

        // Update stock level
        $stockLevel = StockLevel::where('item_id', $challanItem->item_id)
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
            ->select('id', 'item_id', 'warehouse_id', 'available_qty', 'reserved_qty')
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
    public function getStockValuation(Item $item, Warehouse $warehouse): array
    {
        $stockLevel = StockLevel::where('item_id', $item->id)
            ->where('warehouse_id', $warehouse->id)
            ->select('id', 'available_qty', 'reserved_qty')
            ->first();

        if (! $stockLevel || $stockLevel->available_qty <= 0) {
            return [
                'item_id' => $item->id,
                'item_name' => $item->material_name,
                'warehouse_id' => $warehouse->id,
                'quantity' => 0,
                'total_cost' => 0,
                'average_cost' => 0,
                'inventory_value' => 0,
            ];
        }

        return [
            'item_id' => $item->id,
            'item_name' => $item->material_name,
            'warehouse_id' => $warehouse->id,
            'quantity' => $stockLevel->available_qty,
            'reserved_qty' => $stockLevel->reserved_qty,
            'available_qty' => $this->getAvailableStock($item, $warehouse),
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
    public function initializeStockLevel(Item $item, Warehouse $warehouse, float $initialQuantity = 0): StockLevel
    {
        return StockLevel::firstOrCreate(
            [
                'item_id' => $item->id,
                'warehouse_id' => $warehouse->id,
            ],
            [
                'available_qty' => $initialQuantity,
                'reserved_qty' => 0,

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
            'source_type' => Item::class,
            'source_id' => $item->id,
            'movement_type' => 'IN',
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'movement_date' => now(),
            'remarks' => $reference ? "Stock received - Reference: {$reference}" : 'Stock received',
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
    public function receiveStockBatch(array $items, ?string $batchReference = null)
    {
        $movements = collect();

        foreach ($items as $itemData) {
            try {
                $item = Item::findOrFail($itemData['item_id']);
                $warehouse = Warehouse::findOrFail($itemData['warehouse_id']);

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
            ->select(['id', 'movement_type', 'quantity', 'unit_cost', 'remarks', 'created_at'])
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
            $query->select('id', 'item_id', 'warehouse_id', 'available_qty', 'reserved_qty');
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
            ->select('id', 'item_id', 'warehouse_id', 'available_qty', 'reserved_qty');

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
    public function adjustStock(Item $item, Warehouse $warehouse, float $quantityChange, string $reason, ?float $unitCost = null): StockMovement
    {
        $stockLevel = $this->initializeStockLevel($item, $warehouse);

        // Validate adjustment won't make stock negative
        if (($stockLevel->available_qty + $quantityChange) < 0) {
            throw new Exception("Cannot adjust stock below 0 for {$item->material_name}. Current: {$stockLevel->available_qty}");
        }

        $movement = StockMovement::create([
            'item_id' => $item->id,
            'warehouse_id' => $warehouse->id,
            'source_type' => Item::class,
            'source_id' => $item->id,
            'movement_type' => 'ADJUSTMENT',
            'quantity' => $quantityChange,
            'unit_cost' => $unitCost ?? $item->price_per_unit,
            'movement_date' => now(),
            'remarks' => "Adjustment: {$reason}",
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

    public function createInvoice(Challan $challan): Invoice
    {
        $invoice = Invoice::create([
            'party_id' => $challan->party_id,
            'total_amount' => $challan->challan_items->sum('amount') + $challan->driver_bata,
            'driver_bata' => $challan->driver_bata,
            'payment_mode' => $challan->payment_mode ?? PaymentOpts::AC,
            'company_id' => $challan->company_id,
            'invoice_date' => $challan->challan_date
        ]);

        if ($challan->driver_bata > 0) {
            Expense::create([
                'expenditure_date' => $challan->created_at,
                'invoice_id' => $invoice->id,
                'amount' => $challan->driver_bata,
                'category' => ExpenseOpts::DriverBata->value,
                'party_id' => $challan->party->id,
                'notes' => 'Driver Bata for Challan #' . $challan->challan_number,
            ]);
        }


        $challan->update([
            'invoice_id' => $invoice->id,
            'status' => 'Invoiced',
        ]);
        return $invoice;
    }

    public function createOnProductionEntry(ProductionEntry $productionEntry)
    {
        $foundItem = Item::findOrFail($productionEntry->item_id);
        $foundWarehouse = Warehouse::findOrFail($productionEntry->warehouse_id);
        $this->receiveStock($foundItem, $foundWarehouse, $productionEntry->quantity, $foundItem->price_per_unit);
    }

    public function updateOnProductionEntry(ProductionEntry $productionEntry)
    {
        if ($productionEntry->isDirty(['quantity', 'item_id', 'warehouse_id', 'date'])) {
            $foundItem = Item::findOrFail($productionEntry->item_id);
            $foundWarehouse = Warehouse::findOrFail($productionEntry->warehouse_id);
            $this->adjustStock($foundItem, $foundWarehouse, $productionEntry->quantity, $productionEntry->quantity > 0 ? 'Production Entry' : 'Production Return', $foundItem->price_per_unit);
        }
    }

    public function createFromChallans(Collection $challans): Invoice
    {
        if ($challans->isEmpty()) {
            throw ValidationException::withMessages([
                'challans' => 'No challans selected.',
            ]);
        }

        // Load required relations once
        $challans->loadMissing([
            'challan_items.item',
            'party',
        ]);

        $this->validateChallans($challans);

        return DB::transaction(function () use ($challans) {

            $first = $challans->first();
            $totalDriverBata = $challans->sum('driver_bata');

            $invoice = Invoice::create([
                'party_id' => $first->party_id,
                'company_id' => $first->company_id,
                'payment_mode' => $first->payment_mode ?? PaymentOpts::AC,
                'driver_bata' => $totalDriverBata,
                'total_amount' => $this->calculateTotal($challans),
            ]);

            if ($totalDriverBata > 0) {
                Expense::create([
                    'expenditure_date' => $invoice->created_at,
                    'invoice_id' => $invoice->id,
                    'amount' => $totalDriverBata,
                    'description' => 'Driver Bata for Invoice #' . $invoice->invoice_number,
                ]);
            }

            $challans->each(function (Challan $challan) use ($invoice) {

                $challan->update([
                    'invoice_id' => $invoice->id,
                    'status' => 'Invoiced',
                ]);
            });

            return $invoice;
        });
    }

    protected function calculateTotal(Collection $challans): float
    {
        return $challans->sum(function (Challan $challan) {
            $challanItemsTotal = $challan->challan_items->sum('amount');

            return $challanItemsTotal + $challan->driver_bata;
        });
    }

    protected function validateChallans(Collection $challans): void
    {
        $this->ensureSameParty($challans);
        $this->ensureSameCompany($challans);
        $this->ensureNotAlreadyInvoiced($challans);
        $this->ensurePendingStatus($challans);
    }

    protected function ensureSameParty(Collection $challans): void
    {
        if ($challans->pluck('party_id')->unique()->count() > 1) {
            throw ValidationException::withMessages([
                'challans' => 'All challans must belong to the same party.',
            ]);
        }
    }

    protected function ensureSameCompany(Collection $challans): void
    {
        if ($challans->pluck('company_id')->unique()->count() > 1) {
            throw ValidationException::withMessages([
                'challans' => 'All challans must belong to the same company.',
            ]);
        }
    }

    protected function ensurePendingStatus(Collection $challans): void
    {
        $invalid = $challans->first(
            fn(Challan $challan) => $challan->status !== 'Pending'
        );

        if ($invalid) {
            throw ValidationException::withMessages([
                'challans' => "Challan {$invalid->challan_number} is not Pending.",
            ]);
        }
    }

    protected function ensureNotAlreadyInvoiced(Collection $challans): void
    {
        $invalid = $challans->first(
            fn(Challan $challan) => filled($challan->invoice_id)
        );

        if ($invalid) {
            throw ValidationException::withMessages([
                'challans' => "Challan {$invalid->challan_number} is already invoiced.",
            ]);
        }
    }
}
