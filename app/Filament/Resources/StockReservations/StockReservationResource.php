<?php

namespace App\Filament\Resources\StockReservations;

use App\Enums\NavigGroup;
use App\Filament\Resources\StockReservations\Pages\ListStockReservations;
use App\Filament\Resources\StockReservations\Tables\StockReservationsTable;
use App\Models\StockReservation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class StockReservationResource extends Resource
{
    protected static ?string $model = StockReservation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static UnitEnum|string|null $navigationGroup = NavigGroup::Inventory;

    public static function table(Table $table): Table
    {
        return StockReservationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockReservations::route('/'),
        ];
    }
}
