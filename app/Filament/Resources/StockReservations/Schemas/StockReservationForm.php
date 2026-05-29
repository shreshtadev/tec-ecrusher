<?php

namespace App\Filament\Resources\StockReservations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StockReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('source_type')
                    ->required(),
                TextInput::make('source_id')
                    ->required()
                    ->numeric(),
                Select::make('warehouse_id')
                    ->relationship('warehouse', 'name')
                    ->required(),
                Select::make('item_id')
                    ->relationship('item', 'material_name')
                    ->required(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->options(['reserved' => 'Reserved', 'finalized' => 'Finalized', 'cancelled' => 'Cancelled'])
                    ->default('reserved')
                    ->required(),
            ]);
    }
}
