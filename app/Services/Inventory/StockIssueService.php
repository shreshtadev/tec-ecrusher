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
}
