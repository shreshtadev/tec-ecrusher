<?php

namespace App\Filament\Resources\Challans\Schemas;

use App\Enums\PaymentOpts;
use App\Models\Item;
use App\Models\PartyItemPrice;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput; // 1. Imported the Repeater
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ChallanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Challan Details')
                    ->schema([
                        TextInput::make('challan_number')
                            ->hiddenOn('create')
                            ->readonly('edit'),

                        TextInput::make('status')
                            ->readOnly()->default('Pending'),
                    ])->columns(2),

                Section::make('Payment Details')->schema([
                    Select::make('payment_mode')
                        ->options(PaymentOpts::options())->default(PaymentOpts::AC)->native(false),
                    TextInput::make('driver_bata')->numeric()->default(0),
                ]),
                // 2. Added the Repeater Section for Multiple Items
                Section::make('Tripsheets')
                    ->schema([
                        Repeater::make('challan_items') // Matches your Eloquent relationship name
                            ->relationship('challan_items')
                            ->schema([

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
                                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                        $partyId = $get('../../party_id');

                                        $item = Item::find($state);

                                        if (! $item) {
                                            return;
                                        }

                                        $price = PartyItemPrice::query()
                                            ->where('party_id', $partyId)
                                            ->where('item_id', $state)
                                            ->value('price_per_unit');

                                        $set('rate_at_sale', $price ?? $item->price_per_unit);
                                        $set('quantity_cft', 0);
                                        $set('amount', 0);
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
                            ->columns(2) // Arranges fields neatly side-by-side in a row
                            ->defaultItems(1)
                            ->addActionLabel('Add Item'),
                    ]),
                Section::make('Assignments')
                    ->schema([
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
                                $itemId = $get('item_id');
                                $item = Item::where('id', $itemId)->first();
                                $price = PartyItemPrice::query()
                                    ->where('party_id', $state)
                                    ->where('item_id', $itemId)
                                    ->value('price_per_unit');
                                if ($item) {
                                    if (!$price) {
                                        $price ??= $item->price_per_unit;
                                    }
                                    $set('rate_at_sale', $price);
                                    $set('quantity_cft', 0);
                                    $set('amount', 0);
                                }
                                $set('vehicle_id', null);
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
                    ])->columns(2),

            ])->disabled(fn($record) => $record?->status === 'Invoiced');
    }
}
