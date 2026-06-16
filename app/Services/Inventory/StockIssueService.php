<?php

namespace App\Services\Inventory;

use App\Models\StockIssue;
use App\Models\StockLevel;
use App\Models\StockMovement;
use Exception;
use Illuminate\Support\Facades\DB;

class StockIssueService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function issue(StockIssue $stockIssue): void
    {
        DB::transaction(function () use ($stockIssue) {

            $stockIssue->load('items');

            foreach ($stockIssue->items as $issueItem) {

                $stockLevel = StockLevel::query()
                    ->where('item_id', $issueItem->item_id)
                    ->where('warehouse_id', $stockIssue->warehouse_id)
                    ->lockForUpdate()
                    ->first();

                if (! $stockLevel) {
                    throw new Exception(
                        "Stock not found for {$issueItem->item->material_name}"
                    );
                }

                if ($stockLevel->available_qty < $issueItem->quantity) {
                    throw new Exception(
                        "Insufficient stock for {$issueItem->item->material_name}"
                    );
                }

                $stockLevel->decrement(
                    'available_qty',
                    $issueItem->quantity
                );

                StockMovement::create([
                    'item_id'       => $issueItem->item_id,
                    'warehouse_id'  => $stockIssue->warehouse_id,
                    'movement_type' => 'OUT',
                    'quantity'      => $issueItem->quantity,
                    'source_type'   => StockIssue::class,
                    'source_id'     => $stockIssue->id,
                    'movement_date' => $stockIssue->issue_date,
                    'remarks'       => $stockIssue->purpose,
                ]);
            }

            $stockIssue->update([
                'status' => 'issued',
            ]);
        });
    }

    /**
     * Revert an issued StockIssue: restore stock levels and remove movements.
     */
    public function revertIssue(StockIssue $stockIssue): void
    {
        DB::transaction(function () use ($stockIssue) {

            $movements = StockMovement::query()
                ->where('source_type', StockIssue::class)
                ->where('source_id', $stockIssue->id)
                ->lockForUpdate()
                ->get();

            if ($movements->isEmpty()) {
                return;
            }

            foreach ($movements as $movement) {
                $stockLevel = StockLevel::query()
                    ->where('item_id', $movement->item_id)
                    ->where('warehouse_id', $movement->warehouse_id)
                    ->lockForUpdate()
                    ->first();

                if ($stockLevel) {
                    if ($movement->movement_type === 'OUT') {
                        $stockLevel->increment('available_qty', $movement->quantity);
                    } elseif ($movement->movement_type === 'IN') {
                        // If an IN movement exists for this source, try to reverse it conservatively
                        $stockLevel->decrement('available_qty', min($stockLevel->available_qty, $movement->quantity));
                    }
                }

                // remove the movement record (we're rolling back the issue)
                $movement->delete();
            }

            $stockIssue->update([
                'status' => 'cancelled',
            ]);
        });
    }
}
