<?php

namespace App\Filament\Resources\Challans\Schemas;

use App\Enums\PaymentOpts;
use App\Models\Item;
use App\Models\PartyItemPrice;
use App\Models\Vehicle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Fruitcake\LaravelDebugbar\Facades\Debugbar;
use Illuminate\Support\HtmlString;

class ChallanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('challan_number')
                    ->hiddenOn('create')
                    ->readonly('edit'),

                TextInput::make('status')
                    ->hiddenOn('create')
                    ->readOnly()->default('Pending'),

                DateTimePicker::make('challan_date')->required()->default(now())->seconds(false)->native(false),

                Select::make('payment_mode')
                    ->options(PaymentOpts::options())->default(PaymentOpts::AC)->native(false),
                TextInput::make('driver_bata')->numeric()->default(0),
                Select::make('company_id')
                    ->label('Company')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()
                    ->default(fn(Select $component): string => array_key_first($component->getOptions()))
                    ->native(false),

                Select::make('party_id')
                    ->label('Party')
                    ->relationship('party', 'full_name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->disabled(fn(Get $get) => blank($get('company_id')))
                    ->dehydrated()
                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                        Debugbar::info("PartyId: {$state}");
                        $itemId = $get('item_id');
                        Debugbar::info("ItemID: {$itemId}");
                        $item = Item::find($itemId);
                        $price = PartyItemPrice::query()
                            ->where('party_id', $state)
                            ->where('item_id', $itemId)
                            ->value('price_per_unit');
                        Debugbar::info("PriceByParty: {$price}");
                        if ($item) {
                            if (!$price) {
                                $price ??= $item->price_per_unit;
                                Debugbar::info("PriceNotFound: {$price}");
                            }
                            $set('rate_at_sale', $price);
                            $set('quantity_cft', 0);
                            $set('amount', 0);
                        }
                    })
                    ->required()->native(false),

                Select::make('vehicle_id')
                    ->label('Vehicle')
                    ->relationship(name: 'vehicle', titleAttribute: 'vehicle_number')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->disabled(fn(Get $get) => blank($get('party_id')))
                    ->dehydrated()
                    ->placeholder('Select a party first')
                    ->required()
                    ->native(false),

                Select::make('driver_id')
                    ->label('Driver')
                    ->relationship(name: 'driver', titleAttribute: 'full_name')
                    ->native(false)
                    ->placeholder('Select a party first')
                    ->preload()
                    ->live()
                    ->disabled(fn(Get $get) => blank($get('party_id')))
                    ->dehydrated()
                    ->searchable()
                    ->required(),

                Repeater::make('challan_items')->helperText(function (Get $get) {
                    $vehicleId = $get('vehicle_id');
                    if (!blank($vehicleId)) {
                        $foundVehicle = Vehicle::find($vehicleId);
                        $totalAllowed = "<strong>Allowed Quantity: {$foundVehicle->capacity_cft}</strong>";
                        return new HtmlString($totalAllowed);
                    }
                })
                    ->relationship('challan_items')
                    ->schema([
                        Select::make('warehouse_id')
                            ->label('Warehouse')
                            ->relationship(
                                'warehouse',
                                'name',
                                modifyQueryUsing: fn($query) => $query->orderBy('id')
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->default(fn(Select $component) => collect($component->getOptions())->keys()->first())
                            ->native(false),

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
                            ->disabled(fn(Get $get) => blank($get('warehouse_id')))
                            ->dehydrated()
                            ->native(false)
                            ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                Debugbar::info("ITEMID: {$state}");
                                $item = Item::find($state);
                                $partyId = $get('../../party_id');
                                Debugbar::info("PartyFound: {$partyId}");
                                $price = PartyItemPrice::query()
                                    ->where('party_id', $partyId)
                                    ->where('item_id', $state)
                                    ->value('price_per_unit');
                                Debugbar::info("PriceByPartyHere: {$price}");

                                if ($item) {
                                    if (!$price) {
                                        $price ??= $item->price_per_unit;
                                        Debugbar::info("PriceNoPartyHere: {$price}");
                                    }
                                    $set('rate_at_sale', $price);
                                    $set('quantity_cft', 0);
                                    $set('amount', 0);
                                }
                            }),

                        TextInput::make('quantity_cft')
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
                                $quantity = $get('quantity_cft') ?? 0;
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
                    ])
                    ->columns(4) // Arranges fields neatly side-by-side in a row
                    ->columnSpanFull()
                    ->defaultItems(1)
                    ->addActionLabel('Add Item'),

            ])->disabled(fn($record) => $record?->status === 'Invoiced');
    }
}
