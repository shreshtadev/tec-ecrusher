<?php

namespace App\Filament\Resources\StockLevels\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StockLevelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('item_id')
                    ->relationship('item', 'material_name')
                    ->searchable()
                    ->required(),
                Select::make('warehouse_id')
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('available_qty')
                    ->numeric()
                    ->required()
                    ->disabled(),
                TextInput::make('reserved_qty')
                    ->numeric()
                    ->required()
                    ->disabled(),
                Select::make('valuation_method')
                    ->options([
                        'FIFO' => 'FIFO',
                        'LIFO' => 'LIFO',
                    ])
                    ->required(),
            ]);
    }
}
