<?php

namespace App\Filament\Resources\ProductionEntries\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProductionEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('production_entry_date')
                    ->required(),
                Select::make('item_id')
                    ->required()
                    ->options(function () {
                        return \App\Domains\Master\Models\Item::pluck('material_name', 'id');
                    })->native(false),
                Select::make('warehouse_id')
                    ->required()
                    ->options(function () {
                        return \App\Domains\Master\Models\Warehouse::pluck('name', 'id');
                    })->native(false),
                TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                TextInput::make('batch_no'),
            ]);
    }
}
