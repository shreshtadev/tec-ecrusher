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
                    ->relationship('party', 'id')
                    ->required(),
                TextInput::make('vehicle_number')
                    ->required(),
                TextInput::make('capacity_cft')
                    ->required()
                    ->numeric(),
                Select::make('vehicle_type')
                    ->required()
                    ->native(false)
                    ->allowHtml()
                    ->options(
                        collect(self::vehicleTypeOptions())
                            ->mapWithKeys(fn($type, $key) => [
                                $key => "
                    <div style='display:flex; flex-direction:column;'>
                        <span style='font-weight:600;'>
                            {$type['label']}
                        </span>

                        <span style='font-size:11px; color:#6b7280;'>
                            {$type['usage']}
                        </span>
                    </div>
                "
                            ])
                            ->toArray()
                    )
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
}
