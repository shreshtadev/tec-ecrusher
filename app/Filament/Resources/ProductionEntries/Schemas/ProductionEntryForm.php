<?php

namespace App\Filament\Resources\ProductionEntries\Schemas;

use App\Models\Item;
use App\Models\Warehouse;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ProductionEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('production_entry_date'),
                Select::make('item_id')
                    ->label('Item')
                    ->required()
                    ->options(function () {
                        return Item::query()
                            ->get()
                            ->mapWithKeys(function ($item) {
                                return [
                                    $item->id => "{$item->material_name} ({$item->unit})",
                                ];
                            });
                    })
                    ->default(request('item_id'))
                    ->searchable()
                    ->live()
                    ->native(false),
                Select::make('warehouse_id')
                    ->label('Warehouse')
                    ->required()
                    ->options(function () {
                        return Warehouse::pluck('name', 'id');
                    })->native(false)->default(fn (Select $component): string => array_key_first($component->getOptions())),
                TextInput::make('quantity')
                    ->label(function (Get $get) {
                        $itemId = $get('item_id');
                        if (! $itemId) {
                            return 'Quantity';
                        }
                        $item = Item::find($itemId);

                        return $item && $item->unit
                            ? "Quantity ({$item->unit})"
                            : 'Quantity';
                    })
                    ->required()
                    ->numeric(),
                TextInput::make('batch_no')
                    ->prefix('BTH-')
                    ->formatStateUsing(
                        fn (?string $state): string => ($state && ! str_starts_with($state, 'BTH-'))
                            ? "BTH-{$state}"
                            : ($state ?? '')
                    ),
            ]);
    }
}
