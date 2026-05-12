<?php

namespace App\Domains\Master\Services;

use App\Domains\Master\Models\StockAdjustment;
use App\Domains\Master\Models\StockLevel;
use App\Domains\Operations\Models\StockMovement;

class StockAdjustmentService
{
    /**
     * Create a stock adjustment and corresponding movement.
     */
    public function create(array $data): StockAdjustment
    {
        $adjustment = StockAdjustment::create($data);

        // Create corresponding stock movement
        StockMovement::create([
            'item_id' => $adjustment->item_id,
            'warehouse_id' => $adjustment->warehouse_id,
            'adjustment_id' => $adjustment->id,
            'movement_type' => 'ADJUSTMENT',
            'quantity' => $adjustment->quantity_change,
            'unit_cost' => $adjustment->item->price_per_unit,
            'notes' => "Adjustment: {$adjustment->adjustment_type} - {$adjustment->reason}",
        ]);

        // Update stock level
        $stockLevel = StockLevel::where('item_id', $adjustment->item_id)
            ->where('warehouse_id', $adjustment->warehouse_id)
            ->first();

        if ($stockLevel) {
            if ($adjustment->quantity_change > 0) {
                $stockLevel->increment('available_qty', $adjustment->quantity_change);
            } else {
                $stockLevel->decrement('available_qty', abs($adjustment->quantity_change));
            }
        }

        return $adjustment;
    }

    /**
     * Cancel a stock adjustment and reverse its movement.
     */
    public function cancel(StockAdjustment $adjustment): void
    {
        if ($adjustment->trashed()) {
            return;
        }

        // Soft delete the adjustment
        $adjustment->delete();

        // Reverse the stock level change
        $stockLevel = StockLevel::where('item_id', $adjustment->item_id)
            ->where('warehouse_id', $adjustment->warehouse_id)
            ->first();

        if ($stockLevel) {
            // Reverse the adjustment
            if ($adjustment->quantity_change > 0) {
                $stockLevel->decrement('available_qty', $adjustment->quantity_change);
            } else {
                $stockLevel->increment('available_qty', abs($adjustment->quantity_change));
            }
        }

        // Soft delete the related movement
        StockMovement::where('adjustment_id', $adjustment->id)->delete();
    }
}
