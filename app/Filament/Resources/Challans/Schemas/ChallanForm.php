<?php

namespace App\Filament\Resources\Challans\Schemas;

use App\Domains\Common\Enums\PaymentOpts;
use App\Domains\Master\Models\Company;
use App\Domains\Master\Models\Driver;
use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\Vehicle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

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

                Section::make('Assignments')
                    ->schema([
                        Select::make('company_id')
                            ->label('Company')
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->default(function () {
                                return Company::count() === 1 ? Company::first()->id : null;
                            })
                            ->native(false),
                        // 1. Party Select
                        Select::make('party_id')
                            ->label('Party')
                            ->relationship('party', 'full_name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->disabled(fn(Get $get) => blank($get('company_id')))
                            ->dehydrated()
                            ->afterStateUpdated(fn(Set $set) => $set('vehicle_id', null))
                            ->required()->native(false),

                        // 2. Vehicle Select (Filtered by Party)
                        Select::make('vehicle_id')
                            ->label('Vehicle')
                            ->relationship(
                                name: 'vehicle',
                                titleAttribute: 'vehicle_number',
                                modifyQueryUsing: function (Builder $query, Get $get) {
                                    return $query->when(
                                        $get('party_id'),
                                        fn(Builder $query, $partyId) => $query->where('party_id', $partyId),
                                        fn(Builder $query) => $query->whereRaw('1 = 0')
                                    );
                                }
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->disabled(fn(Get $get) => blank($get('party_id')))
                            ->dehydrated()
                            ->afterStateUpdated(function ($state, Set $set) {
                                $vehicle = Vehicle::find($state);

                                if ($vehicle) {
                                    $set('quantity_cft', $vehicle->capacity_cft);
                                }
                            })
                            ->placeholder('Select a party first')
                            ->required()
                            ->native(false),

                        // 3. Driver Select (Filtered by Party)
                        Select::make('driver_id')
                            ->label('Driver')
                            ->relationship(name: 'driver', titleAttribute: 'full_name', modifyQueryUsing: function (Builder $query, Get $get) {
                                return $query->when(
                                    $get('party_id'),
                                    fn(Builder $query, $partyId) => $query->where('party_id', $partyId),
                                    fn(Builder $query) => $query->whereRaw('1 = 0')
                                );
                            })
                            ->native(false)
                            ->placeholder('Select a party first')
                            ->preload()
                            ->live()
                            ->disabled(fn(Get $get) => blank($get('party_id')))
                            ->dehydrated()
                            ->searchable()
                            ->required(),

                        // 4. Item Select
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
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $item = Item::find($state);

                                if ($item) {
                                    $set('rate_at_sale', $item->price_per_unit);

                                    $quantity = $get('quantity_cft') ?? 0;
                                    $set('amount', $item->price_per_unit * $quantity);
                                }
                            }),

                        TextInput::make('quantity_cft')
                            ->label('Quantity')
                            ->live()
                            ->numeric()
                            ->required()
                            ->prefix(function (Get $get) {
                                $itemId = $get('item_id');

                                if (! $itemId) {
                                    return null;
                                }

                                return Item::find($itemId)?->unit;
                            })
                            ->placeholder('Select an item first')
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
                            ->numeric()
                            ->prefix('₹')
                            ->required()
                            ->readOnly()
                            ->default(0),
                    ])->columns(2),

            ])->disabled(fn($record) => $record?->status === 'Invoiced');
    }
}
