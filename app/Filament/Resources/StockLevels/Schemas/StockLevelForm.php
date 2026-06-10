<?php

namespace App\Filament\Resources\StockLevels\Schemas;

use App\Models\Item;
use App\Models\Warehouse;
use App\Services\StockService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StockLevelForm
{
    public static function configure(Schema $schema, ?StockService $stockService = null): Schema
    {
        return $schema
            ->components([
                Select::make('item_id')
                    ->relationship('item', 'material_name')
                    ->searchable()
                    ->required(),
                Select::make('warehouse_id')
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('available_qty')
                    ->numeric()
                    ->required()
                    ->disabled(),
                TextInput::make('reserved_qty')
                    ->numeric()
                    ->required()
                    ->disabled(),

                TextEntry::make('stock_level_valuation')
                    ->state(function (callable $get) use ($stockService) {
                        $itemId = $get('item_id');
                        $warehouseId = $get('warehouse_id');

                        if (! $itemId || ! $warehouseId) {
                            return 'Select item and warehouse to view current stock valuation.';
                        }

                        $item = Item::whereKey($itemId)->first();
                        $warehouse = Warehouse::whereKey($warehouseId)->first();

                        if (! $item || ! $warehouse) {
                            return 'Unable to resolve selected item or warehouse.';
                        }

                        $service = $stockService ?? app(StockService::class);
                        $valuation = $service->getStockValuation($item, $warehouse);
                        $available = $service->getAvailableStock($item, $warehouse);

                        return sprintf(
                            'Available qty: %s<br>Reserved qty: %s<br>On-hand qty: %s',
                            $available,
                            $valuation['reserved_qty'] ?? 0,
                            $valuation['quantity'] ?? 0,
                        );
                    })
                    ->html(),
            ]);
    }
}
