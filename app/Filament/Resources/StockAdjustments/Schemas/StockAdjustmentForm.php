<?php

namespace App\Filament\Resources\StockAdjustments\Schemas;

use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\Warehouse;
use App\Domains\Operations\Services\StockService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StockAdjustmentForm
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
                Select::make('adjustment_type')
                    ->options([
                        'Damage' => 'Damage',
                        'Loss' => 'Loss',
                        'Correction' => 'Correction',
                        'Audit' => 'Audit',
                        'Other' => 'Other',
                    ])
                    ->required(),
                TextInput::make('quantity_change')
                    ->numeric()
                    ->required()
                    ->label('Quantity Change (can be negative)'),
                TextEntry::make('stock_adjustment_preview')
                    ->state(function (callable $get) use ($stockService) {
                        $itemId = $get('item_id');
                        $warehouseId = $get('warehouse_id');
                        $quantityChange = $get('quantity_change');

                        if (! $itemId || ! $warehouseId) {
                            return 'Select item and warehouse to preview the stock impact before saving.';
                        }

                        $item = Item::whereKey($itemId)->first();
                        $warehouse = Warehouse::whereKey($warehouseId)->first();

                        if (! $item || ! $warehouse) {
                            return 'Unable to resolve selected item or warehouse.';
                        }

                        $service = $stockService ?? app(StockService::class);
                        $currentAvailable = $service->getAvailableStock($item, $warehouse);

                        if ($quantityChange === null) {
                            return sprintf(
                                'Current available qty: %s<br>Enter a quantity change to preview the impact.',
                                $currentAvailable,
                            );
                        }

                        $projected = $currentAvailable + $quantityChange;
                        $status = $projected <= 0
                            ? 'Warning: stock will be depleted or negative after adjustment.'
                            : 'Projected stock remains positive after adjustment.';

                        return sprintf(
                            'Current available qty: %s<br>Projected available qty: %s<br>%s',
                            $currentAvailable,
                            $projected,
                            $status,
                        );
                    })
                    ->html(),
                Textarea::make('reason')
                    ->required()
                    ->rows(3),
                TextInput::make('reference_number')
                    ->label('Reference Number')
                    ->maxLength(255),
            ]);
    }
}
