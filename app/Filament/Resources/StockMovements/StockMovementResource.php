<?php

namespace App\Filament\Resources\StockMovements;

use App\Domains\Common\Enums\NavigGroup;
use App\Domains\Operations\Models\StockMovement;
use App\Filament\Resources\StockMovements\Pages\CreateStockMovements;
use App\Filament\Resources\StockMovements\Pages\EditStockMovements;
use App\Filament\Resources\StockMovements\Pages\ListStockMovements;
use App\Filament\Resources\StockMovements\Schemas\StockMovementForm;
use App\Filament\Resources\StockMovements\Tables\StockMovementsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Stock Movements';

    protected static ?string $modelLabel = 'Stock Movement';

    protected static UnitEnum|string|null $navigationGroup = NavigGroup::Inventory;

    public static function form(Schema $schema): Schema
    {
        return StockMovementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockMovementsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockMovements::route('/'),
            'create' => CreateStockMovements::route('/create'),
            'edit' => EditStockMovements::route('/{record}/edit'),
        ];
    }
}
