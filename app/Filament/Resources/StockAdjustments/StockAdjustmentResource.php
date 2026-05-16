<?php

namespace App\Filament\Resources\StockAdjustments;

use App\Domains\Common\Enums\NavigGroup;
use App\Domains\Master\Models\StockAdjustment;
use App\Domains\Operations\Services\StockService;
use App\Filament\Resources\StockAdjustments\Pages\CreateStockAdjustment;
use App\Filament\Resources\StockAdjustments\Pages\EditStockAdjustment;
use App\Filament\Resources\StockAdjustments\Pages\ListStockAdjustments;
use App\Filament\Resources\StockAdjustments\Schemas\StockAdjustmentForm;
use App\Filament\Resources\StockAdjustments\Tables\StockAdjustmentsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class StockAdjustmentResource extends Resource
{
    protected static ?string $model = StockAdjustment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Stock Adjustments';

    protected static ?string $modelLabel = 'Stock Adjustment';

    protected static UnitEnum|string|null $navigationGroup = NavigGroup::Inventory;

    public static function form(Schema $schema): Schema
    {
        return StockAdjustmentForm::configure($schema, app(StockService::class));
    }

    public static function table(Table $table): Table
    {
        return StockAdjustmentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockAdjustments::route('/'),
            'create' => CreateStockAdjustment::route('/create'),
            'edit' => EditStockAdjustment::route('/{record}/edit'),
        ];
    }
}
