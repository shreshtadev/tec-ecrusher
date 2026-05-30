<?php

namespace App\Domains\Common\Enums;

class VehicleOpts
{
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
