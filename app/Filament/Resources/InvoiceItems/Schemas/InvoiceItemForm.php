<?php

namespace App\Filament\Resources\InvoiceItems\Schemas;

use App\Models\Item;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class InvoiceItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('warehouse_id')
                    ->label('Warehouse')
                    ->relationship(name: 'warehouse', titleAttribute: 'name', modifyQueryUsing: fn($query) => $query->orderBy('name')),
                Select::make('item_id')
                    ->label('Item')
                    ->relationship(
                        name: 'item',
                        titleAttribute: 'material_name',
                        modifyQueryUsing: fn($query) => $query->orderBy('material_name')
                    )
                    ->placeholder('Select an item')
                    ->preload()
                    ->searchable()
                    ->live()
                    ->required()
                    ->native(false)
                    ->afterStateUpdated(function ($state, Set $set) {
                        $item = Item::find($state);
                        if ($item) {
                            $set('rate_at_sale', $item->price_per_unit);
                            $set('quantity', 0);
                            $set('amount', 0);
                        }
                    }),

                TextInput::make('quantity')
                    ->label('Quantity')
                    ->live()
                    ->numeric()
                    ->required()
                    ->prefix(function (Get $get) {
                        $itemId = $get('item_id');

                        return $itemId ? Item::find($itemId)?->unit : null;
                    })
                    ->readOnly(fn(Get $get) => blank($get('item_id')))
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        $rate = $get('rate_at_sale') ?? 0;
                        $set('amount', $rate * ($state ?? 0));
                    }),

                TextInput::make('rate_at_sale')
                    ->label('Rate at Sale')
                    ->numeric()
                    ->live()
                    ->prefix('₹')
                    ->required()
                    ->readOnly(fn(Get $get) => blank($get('item_id')))
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        $quantity = $get('quantity') ?? 0;
                        $set('amount', ($state ?? 0) * $quantity);
                    }),

                TextInput::make('amount')
                    ->label('Amount')
                    ->live()
                    ->numeric()
                    ->prefix('₹')
                    ->required()
                    ->readOnly()
                    ->default(0),
            ]);
    }
}
