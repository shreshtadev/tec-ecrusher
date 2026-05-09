<?php

namespace App\Filament\Resources\Items\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('material_name')
                    ->required(),
                TextInput::make('price_per_unit')
                    ->required()
                    ->numeric(),
                Select::make('unit')
                    ->required()
                    ->default('CFT')
                    ->options(
                        collect(self::unitOptions())
                            ->mapWithKeys(fn($unit, $key) => [
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
            ]);
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
