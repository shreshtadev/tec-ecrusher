<?php

namespace App\Filament\Resources\Challans\Schemas;

use App\Domains\Master\Models\Driver;
use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\Vehicle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                            ->default(fn() => 'CHL-' . strtoupper(uniqid())) // Or your custom logic
                            ->readonly()
                            ->required(),

                        Select::make('status')
                            ->options([
                                'Pending' => 'Pending',
                                'Invoiced' => 'Invoiced',
                            ])
                            ->default('Pending')
                            ->native(false),
                    ])->columns(2),
                Section::make('Payment Details')->schema([
                    Select::make('payment_mode')
                        ->options([
                            'Cash' => 'Cash',
                            'A/C' => 'A/C',
                            'Credit Card' => 'Credit Card',
                            'Bank Transfer' => 'Bank Transfer',
                            'Mobile Payment' => 'Mobile Payment',
                            'Cheque' => 'Cheque',
                            'Other' => 'Other',
                        ])->default('A/C')->native(false),
                ]),

                Section::make('Assignments')
                    ->schema([
                        // 1. Party Select
                        Select::make('party_id')
                            ->label('Party')
                            ->relationship('party', 'full_name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn(Set $set) => $set('vehicle_id', null))
                            ->required(),

                        // 2. Vehicle Select (Filtered by Party)
                        Select::make('vehicle_id')
                            ->label('Vehicle')
                            ->options(function (Get $get) {
                                $partyId = $get('party_id');
                                if (! $partyId) {
                                    return [];
                                }

                                return Vehicle::where('party_id', $partyId)->pluck('vehicle_number', 'id');
                            })
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                // Auto-fill quantity based on Vehicle Master capacity
                                $vehicle = Vehicle::find($state);
                                if ($vehicle) {
                                    $set('quantity_cft', $vehicle->capacity_cft);
                                }
                            })
                            ->relationship('vehicle', 'vehicle_number')
                            ->required(),

                        // 3. Driver Select (Filtered by Party)
                        Select::make('driver_id')
                            ->label('Driver')
                            ->options(function (Get $get) {
                                $partyId = $get('party_id');
                                if (! $partyId) {
                                    return [];
                                }

                                return Driver::where('party_id', $partyId)->pluck('full_name', 'id');
                            })
                            ->relationship('driver', 'full_name')
                            ->searchable()
                            ->required(),

                        // 4. Item Select
                        Select::make('item_id')
                            ->label('Material Item')
                            ->relationship('item', 'material_name')
                            ->searchable()
                            ->required(),

                        TextInput::make('quantity_cft')
                            ->label('Quantity')
                            ->numeric()
                            ->required()
                            ->prefix(function (Get $get) {
                                $itemId = $get('item_id');

                                if (! $itemId) {
                                    return null;
                                }

                                return Item::find($itemId)?->unit;
                            })->helperText(function (Get $get) {
                                $itemId = $get('item_id');

                                if (! $itemId) {
                                    return null;
                                }

                                $item = Item::find($itemId);

                                return "Price per {$item->unit}: ₹{$item->price_per_unit} | Total: ₹" . ($item->price_per_unit * ($get('quantity_cft') ?? 0));
                            }),
                    ])->columns(2),

            ])->disabled(fn($record) => $record?->status === 'Invoiced');
    }
}
