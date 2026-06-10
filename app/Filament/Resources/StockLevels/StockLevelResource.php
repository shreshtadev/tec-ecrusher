<?php

namespace App\Filament\Resources\StockLevels;

use App\Enums\NavigGroup;
use App\Filament\Resources\StockLevels\Pages\ListStockLevels;
use App\Filament\Resources\StockLevels\Tables\StockLevelsTable;
use App\Models\StockLevel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class StockLevelResource extends Resource
{
    protected static ?string $model = StockLevel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Stock Levels';

    protected static ?string $modelLabel = 'Stock Level';

    protected static UnitEnum|string|null $navigationGroup = NavigGroup::Inventory;

    public static function table(Table $table): Table
    {
        return StockLevelsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockLevels::route('/'),
        ];
    }
}
