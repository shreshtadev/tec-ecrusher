<?php

namespace App\Filament\Resources\Challans\Schemas;

use App\Enums\PaymentOpts;
use App\Models\Item;
use App\Models\PartyItemPrice;
use App\Models\StockLevel;
use App\Models\Vehicle;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Grid;

class ChallanForm
{
    private static function getStockAvailabilityMessage(Get $get): HtmlString
    {
        $itemId = $get('item_id');
        $warehouseId = $get('warehouse_id');

        if (blank($itemId)) {
            return new HtmlString('<span style="color: #64748b;">Select an item to view stock availability.</span>');
        }

        $stockLevels = StockLevel::query()
            ->where('item_id', $itemId)
            ->with('warehouse')
            ->get();

        $selectedWarehouseQty = (float) $stockLevels
            ->firstWhere('warehouse_id', $warehouseId)?->available_qty ?? 0;

        $totalAcrossWarehouses = (float) $stockLevels->sum('available_qty');

        return new HtmlString(
            '<strong>Available:</strong> ' . number_format($selectedWarehouseQty, 2) . ' in selected warehouse<br>'
                . '<strong>Total across warehouses:</strong> ' . number_format($totalAcrossWarehouses, 2)
        );
    }

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

                Group::make()
                    ->statePath('challan_date')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // 1. Date Dropdown
                                DatePicker::make('challan_day')
                                    ->label('Challan Date')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d-m-Y')
                                    ->format('Y-m-d')
                                    ->disabled(fn(string $operation) => $operation === 'edit'),

                                // 2. Dynamic Time Dropdown (Generates 12 hours with exact minute increments)
                                TimePicker::make('challan_time')
                                    ->label('Time')
                                    ->seconds(false)
                                    ->displayFormat('h:i A')->disabled(fn(string $operation) => $operation === 'edit'),
                            ])
                    ])
                    // Formats default selections for the Create form using the current time
                    ->default(function () {
                        $now = now();
                        return [
                            'challan_day' => $now->format('Y-m-d'),
                            'challan_time' => $now->format('h:i A'),
                        ];
                    })
                    // Combines the dropdown selections into a standard database timestamp string
                    ->dehydrateStateUsing(function ($state) {
                        $date = $state['challan_day'] ?? null;
                        $time = $state['challan_time'] ?? null;

                        if ($date && $time) {
                            return Carbon::parse("{$date} {$time}")->format('Y-m-d H:i:s');
                        }

                        return null;
                    })
                    // Safely reads the database record and splits it into the fields during Edit mode
                    ->afterStateHydrated(function ($component, $state) {
                        if (is_string($state)) {
                            $carbonDate = Carbon::parse($state);

                            $component->state([
                                'challan_day' => $carbonDate->format('Y-m-d'),
                                'challan_time' => $carbonDate->format('H:i'),
                                'challan_ampm' => $carbonDate->format('A'),
                            ]);
                        }
                    }),

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
                        $itemId = $get('item_id');
                        $item = Item::find($itemId);
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
                            ->helperText(fn(Get $get): HtmlString => self::getStockAvailabilityMessage($get))
                            ->native(false)
                            ->afterStateUpdated(function ($state, Get $get, Set $set) {

                                $item = Item::find($state);
                                $partyId = $get('../../party_id');
                                $price = PartyItemPrice::query()
                                    ->where('party_id', $partyId)
                                    ->where('item_id', $state)
                                    ->value('price_per_unit');

                                if ($item) {
                                    if (!$price) {
                                        $price ??= $item->price_per_unit;
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
