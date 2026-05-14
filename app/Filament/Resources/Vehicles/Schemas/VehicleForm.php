<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VehicleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('party_id')
                    ->relationship('party', 'full_name')
                    ->required(),
                TextInput::make('vehicle_number')
                    ->required(),
                TextInput::make('capacity_cft')
                    ->label('Capacity')
                    ->required()
                    ->numeric(),
                Select::make('unit')
                    ->required()
                    ->default('CFT')
                    ->options(
                        collect(self::unitOptions())
                            ->mapWithKeys(fn ($unit, $key) => [
                                $key => "
                <div style='display:flex; flex-direction:column;'>
                    <span style='font-weight:600;'>
                        {$unit['label']} ({$key})
                    </span>

                    <span style='font-size:11px; color:#6b7280;'>
                        Typical: {$unit['usage']}
                    </span>
                </div>
            ",
                            ])
                            ->toArray()
                    )->allowHtml()->native(false),
                Select::make('vehicle_type')
                    ->required()
                    ->native(false)
                    ->allowHtml()
                    ->options(
                        collect(self::vehicleTypeOptions())
                            ->mapWithKeys(fn ($type, $key) => [
                                $key => "
                    <div style='display:flex; flex-direction:column;'>
                        <span style='font-weight:600;'>
                            {$type['label']}
                        </span>

                        <span style='font-size:11px; color:#6b7280;'>
                            {$type['usage']}
                        </span>
                    </div>
                ",
                            ])
                            ->toArray()
                    ),
            ]);
    }

    public static function vehicleTypeOptions(): array
    {
        return [
            'TIPPER' => [
                'label' => 'Tipper Truck',
                'usage' => 'Crusher materials, jelly, sand transport',
            ],

            'LORRY' => [
                'label' => 'Lorry',
                'usage' => 'General heavy material transport',
            ],

            'TATA_407' => [
                'label' => 'Tata 407',
                'usage' => 'Small load deliveries',
            ],

            'TRACTOR' => [
                'label' => 'Tractor',
                'usage' => 'Local quarry and village transport',
            ],

            'TRAILER' => [
                'label' => 'Trailer Truck',
                'usage' => 'High-capacity bulk transport',
            ],

            'DUMPER' => [
                'label' => 'Dumper',
                'usage' => 'Mining and quarry operations',
            ],

            'PICKUP' => [
                'label' => 'Pickup Vehicle',
                'usage' => 'Lightweight material delivery',
            ],

            'MINI_TRUCK' => [
                'label' => 'Mini Truck',
                'usage' => 'Short-distance construction supply',
            ],

            'WATER_TANKER' => [
                'label' => 'Water Tanker',
                'usage' => 'Dust suppression and water supply',
            ],

            'FUEL_TANKER' => [
                'label' => 'Fuel Tanker',
                'usage' => 'Diesel and fuel transport',
            ],

            'EXCAVATOR' => [
                'label' => 'Excavator',
                'usage' => 'Loading and quarry excavation',
            ],

            'JCB' => [
                'label' => 'JCB / Backhoe Loader',
                'usage' => 'Loading and site operations',
            ],

            'OTHER' => [
                'label' => 'Other',
                'usage' => 'Custom or uncommon vehicle types',
            ],
        ];
    }

    public static function unitOptions(): array
    {
        return [
            'CFT' => [
                'label' => 'Cubic Feet',
                'usage' => 'Jelly, sand, dust',
            ],

            'M3' => [
                'label' => 'Cubic Meter',
                'usage' => 'Concrete, aggregates, engineering measurements',
            ],

            'SQFT' => [
                'label' => 'Square Feet',
                'usage' => 'Tiles, flooring, sheet materials',
            ],

            'MT' => [
                'label' => 'Metric Ton',
                'usage' => 'Crusher materials, bulk aggregates',
            ],

            'TON' => [
                'label' => 'Ton',
                'usage' => 'Bulk transport materials',
            ],

            'KG' => [
                'label' => 'Kilogram',
                'usage' => 'Cement, additives, chemicals',
            ],

            'BAG' => [
                'label' => 'Bag',
                'usage' => 'Cement bags, packaged materials',
            ],

            'LOAD' => [
                'label' => 'Vehicle Load',
                'usage' => 'Truck-based billing and transport',
            ],

            'NOS' => [
                'label' => 'Numbers',
                'usage' => 'Countable items, blocks, pipes',
            ],

            'LTR' => [
                'label' => 'Litres',
                'usage' => 'Diesel, oil, liquid materials',
            ],

            'PCS' => [
                'label' => 'Pieces',
                'usage' => 'Manufactured or discrete items',
            ],

            'UNIT' => [
                'label' => 'Unit',
                'usage' => 'Generic item measurements',
            ],
        ];
    }
}
