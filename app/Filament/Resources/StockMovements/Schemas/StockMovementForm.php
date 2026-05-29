<?php

namespace App\Filament\Resources\StockMovements\Schemas;

use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\Warehouse;
use App\Domains\Operations\Services\StockService;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StockMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('stock_movement_notice')
                    ->state(fn (callable $get) => 'Stock movements are read-only audit records.'),
                TextEntry::make('stock_movement_summary')
                    ->state(function (callable $get) {
                        $itemId = $get('item_id');
                        $warehouseId = $get('warehouse_id');
                        $movementType = $get('movement_type');

                        if (! $itemId || ! $warehouseId) {
                            return 'Select item and warehouse to view stock metrics.';
                        }

                        $item = Item::whereKey($itemId)->first();
                        $warehouse = Warehouse::whereKey($warehouseId)->first();

                        if (! $item || ! $warehouse) {
                            return 'Item or warehouse could not be resolved.';
                        }

                        $service = app(StockService::class);
                        $valuation = $service->getStockValuation($item, $warehouse);
                        $available = $service->getAvailableStock($item, $warehouse);

                        $onHandQty = $valuation['quantity'] ?? 0;

                        return "Available Qty: {$available}<br>On-hand Qty: {$onHandQty}<br>Movement Type: {$movementType}";
                    })
                    ->html(),
            ]);
    }
}
