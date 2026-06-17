<?php

namespace App\Filament\Resources\StockIssues\Schemas;

use App\Models\Item;
use App\Models\StockLevel;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class StockIssueItemRelationForm
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make("item_id")->relationship('item', 'material_name')->required()->live()->native(false)->afterStateUpdated(function ($state, Get $get, Set $set, RelationManager $livewire) {
                $warehouseId = $livewire->getOwnerRecord()->warehouse_id;
                logger()->debug("Warehouse: {$warehouseId}");
                $itemId = $get('item_id');
                logger()->debug("Item: {$itemId}");
                $availableQuantity = StockLevel::where(['warehouse_id' => $warehouseId, 'item_id' => $itemId])->first()->value('available_qty');
                logger()->debug("Available Quantity: {$availableQuantity}");
                $set('available_quantity', $availableQuantity);
            }),
            Hidden::make("available_quantity")
                ->default(0)->dehydrated(false),
            TextInput::make("quantity")->numeric()->live()->maxValue(fn(Get $get) => (float) $get('available_quantity'))
                ->helperText(fn(Get $get) => 'Available: ' . number_format($get('available_quantity'), 0))->default(1),
        ]);
    }
}
