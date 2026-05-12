<?php

namespace App\Domains\Operations\Services;

use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\StockLevel;
use App\Domains\Master\Models\Warehouse;
use App\Domains\Operations\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class StockReportingService
{
    /**
     * Get stock level report for all items in a warehouse.
     */
    public function getStockLevelReport(?Warehouse $warehouse = null): Collection
    {
        $query = StockLevel::with(['item', 'warehouse']);

        if ($warehouse) {
            $query->where('warehouse_id', $warehouse->id);
        }

        return $query->get()->map(function ($stockLevel) {
            return [
                'item_name' => $stockLevel->item->material_name,
                'item_unit' => $stockLevel->item->unit,
                'warehouse' => $stockLevel->warehouse->name,
                'available_qty' => $stockLevel->available_qty,
                'reserved_qty' => $stockLevel->reserved_qty,
                'total_qty' => $stockLevel->available_qty + $stockLevel->reserved_qty,
                'price_per_unit' => $stockLevel->item->price_per_unit,
                'stock_value' => $stockLevel->available_qty * $stockLevel->item->price_per_unit,
            ];
        });
    }

    /**
     * Get stock movement audit trail.
     */
    public function getMovementReport(
        ?Carbon $fromDate = null,
        ?Carbon $toDate = null,
        ?Item $item = null,
        ?Warehouse $warehouse = null
    ): Collection {
        $query = StockMovement::with(['item', 'warehouse', 'challan', 'invoice', 'adjustment']);

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        if ($item) {
            $query->where('item_id', $item->id);
        }

        if ($warehouse) {
            $query->where('warehouse_id', $warehouse->id);
        }

        return $query->orderBy('created_at', 'desc')->get()->map(function ($movement) {
            return [
                'date' => $movement->created_at->format('Y-m-d H:i:s'),
                'item' => $movement->item->material_name,
                'warehouse' => $movement->warehouse->name,
                'movement_type' => $movement->movement_type,
                'quantity' => $movement->quantity,
                'unit_cost' => $movement->unit_cost,
                'total_cost' => $movement->quantity * ($movement->unit_cost ?? 0),
                'reference' => $movement->challan_id ? "Challan #{$movement->challan->challan_number}" : ($movement->invoice_id ? "Invoice #{$movement->invoice->invoice_number}" : ($movement->adjustment_id ? "Adjustment #{$movement->adjustment->id}" : 'Manual')),
                'notes' => $movement->notes,
            ];
        });
    }

    /**
     * Get stock aging report - stock quantity by purchase/receipt date (FIFO-based).
     */
    public function getStockAgingReport(Item $item, Warehouse $warehouse): SupportCollection
    {
        $movements = StockMovement::where('item_id', $item->id)
            ->where('warehouse_id', $warehouse->id)
            ->whereIn('movement_type', ['IN', 'ADJUSTMENT'])
            ->orderBy('created_at', 'asc')
            ->get();

        $currentStockQty = StockLevel::where('item_id', $item->id)
            ->where('warehouse_id', $warehouse->id)
            ->first()?->available_qty ?? 0;

        $agingData = [];
        $remainingQty = $currentStockQty;

        foreach ($movements as $movement) {
            if ($remainingQty <= 0) {
                break;
            }

            $qtyInThisBatch = min($movement->quantity, $remainingQty);
            $ageInDays = now()->diffInDays($movement->created_at);

            $agingData[] = [
                'receipt_date' => $movement->created_at->format('Y-m-d'),
                'quantity' => $qtyInThisBatch,
                'unit_cost' => $movement->unit_cost,
                'total_cost' => $qtyInThisBatch * ($movement->unit_cost ?? 0),
                'age_days' => $ageInDays,
                'age_category' => $this->categorizeAge($ageInDays),
            ];

            $remainingQty -= $qtyInThisBatch;
        }

        return collect($agingData);
    }

    /**
     * Get item-wise costing report (cost breakdown by warehouse and valuation method).
     */
    public function getItemCostingReport(Item $item): SupportCollection
    {
        $stockLevels = StockLevel::where('item_id', $item->id)
            ->with('warehouse')
            ->get();

        $costingData = [];

        foreach ($stockLevels as $stockLevel) {
            $fifoValuation = $this->calculateValuation($item, $stockLevel->warehouse, 'FIFO');
            $lifoValuation = $this->calculateValuation($item, $stockLevel->warehouse, 'LIFO');

            $costingData[] = [
                'item' => $item->material_name,
                'warehouse' => $stockLevel->warehouse->name,
                'available_qty' => $stockLevel->available_qty,
                'valuation_method' => $stockLevel->valuation_method,
                'fifo_total_cost' => $fifoValuation['total_cost'],
                'fifo_unit_cost' => $fifoValuation['average_cost'],
                'lifo_total_cost' => $lifoValuation['total_cost'],
                'lifo_unit_cost' => $lifoValuation['average_cost'],
            ];
        }

        return collect($costingData);
    }

    /**
     * Calculate valuation using FIFO or LIFO method.
     */
    private function calculateValuation(Item $item, Warehouse $warehouse, string $method): array
    {
        $stockLevel = StockLevel::where('item_id', $item->id)
            ->where('warehouse_id', $warehouse->id)
            ->first();

        if (! $stockLevel || $stockLevel->available_qty <= 0) {
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
     * Categorize age in days.
     */
    private function categorizeAge(int $ageInDays): string
    {
        if ($ageInDays <= 30) {
            return '0-30 days';
        } elseif ($ageInDays <= 90) {
            return '31-90 days';
        } elseif ($ageInDays <= 180) {
            return '91-180 days';
        } else {
            return '180+ days';
        }
    }
}
