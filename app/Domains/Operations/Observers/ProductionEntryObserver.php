<?php

namespace App\Domains\Operations\Observers;

use App\Domains\Operations\Models\ProductionEntry;

class ProductionEntryObserver
{
    /**
     * Handle the ProductionEntry "created" event.
     */
    public function created(ProductionEntry $productionEntry)
    {
        // Create the stock addition
        $productionEntry->stockAdjustment()->create([
            'item_id'      => $productionEntry->item_id,
            'warehouse_id' => $productionEntry->warehouse_id,
            'quantity_change'     => $productionEntry->quantity,
            'adjustment_type'         => 'Other',
            'reference_number' => "PE-{$productionEntry->id}",
            'reason'    => "Production Record #{$productionEntry->id}",
        ]);
    }

    /**
     * Handle the ProductionEntry "updated" event.
     */
    public function updated(ProductionEntry $productionEntry)
    {
        // Sync changes if quantity or item changes
        if ($productionEntry->isDirty(['quantity', 'item_id', 'warehouse_id', 'date'])) {
            $productionEntry->stockAdjustment()->update([
                'quantity_change'     => $productionEntry->quantity,
                'item_id'      => $productionEntry->item_id,
                'warehouse_id' => $productionEntry->warehouse_id,
                'date'         => $productionEntry->production_entry_date,
            ]);
        }
    }

    /**
     * Handle the ProductionEntry "deleted" (Soft Delete).
     */
    public function deleted(ProductionEntry $productionEntry)
    {
        // Soft delete the adjustment so it doesn't count toward stock
        $productionEntry->stockAdjustment()->delete();
    }

    /**
     * Handle the ProductionEntry "restored".
     */
    public function restored(ProductionEntry $productionEntry)
    {
        // Bring the stock adjustment back
        $productionEntry->stockAdjustment()->restore();
    }

    /**
     * Handle the ProductionEntry "force deleted".
     */
    public function forceDeleted(ProductionEntry $productionEntry)
    {
        // Permanently remove the stock adjustment
        $productionEntry->stockAdjustment()->forceDelete();
    }
}
