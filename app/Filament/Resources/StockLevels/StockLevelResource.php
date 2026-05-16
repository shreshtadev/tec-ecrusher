<?php

namespace App\Filament\Resources\StockLevels;

use App\Domains\Common\Enums\NavigGroup;
use App\Domains\Master\Models\StockLevel;
use App\Domains\Operations\Services\StockService;
use App\Filament\Resources\StockLevels\Pages\CreateStockLevel;
use App\Filament\Resources\StockLevels\Pages\EditStockLevel;
use App\Filament\Resources\StockLevels\Pages\ListStockLevels;
use App\Filament\Resources\StockLevels\Schemas\StockLevelForm;
use App\Filament\Resources\StockLevels\Tables\StockLevelsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
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

    public static function form(Schema $schema): Schema
    {
        return StockLevelForm::configure($schema, app(StockService::class));
    }

    public static function table(Table $table): Table
    {
        return StockLevelsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockLevels::route('/'),
            'create' => CreateStockLevel::route('/create'),
            'edit' => EditStockLevel::route('/{record}/edit'),
        ];
    }
}
