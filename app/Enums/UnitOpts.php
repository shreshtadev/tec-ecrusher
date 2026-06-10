<?php

namespace App\Enums;

class UnitOpts
{
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
