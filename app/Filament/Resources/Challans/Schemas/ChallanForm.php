<?php

namespace App\Filament\Resources\Challans\Schemas;

use App\Domains\Master\Models\Driver;
use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\Vehicle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
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
                            ->native(false)
                            ->disabled(fn($record) => $record?->status === 'Invoiced'),
                    ])->columns(2),

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
                                if (!$partyId) return [];
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
                            ->required(),

                        // 3. Driver Select (Filtered by Party)
                        Select::make('driver_id')
                            ->label('Driver')
                            ->options(function (Get $get) {
                                $partyId = $get('party_id');
                                if (!$partyId) return [];
                                return Driver::where('party_id', $partyId)->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required(),

                        // 4. Item Select
                        Select::make('item_id')
                            ->label('Material Item')
                            ->relationship('item', 'material_name')
                            ->searchable()
                            ->required(),

                        TextInput::make('quantity_cft')
                            ->label('Quantity (CFT)')
                            ->numeric()
                            ->required()
                            ->prefix('CFT'),
                    ])->columns(2),
            ]);
    }
}
