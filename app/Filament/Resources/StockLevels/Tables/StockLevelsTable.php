<?php

namespace App\Filament\Resources\StockLevels\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StockLevelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item.material_name')
                    ->label('Item')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('warehouse.name')
                    ->label('Warehouse')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('available_qty')
                    ->label('Available')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('reserved_qty')
                    ->label('Reserved')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('warehouse_id')
                    ->relationship('warehouse', 'name')
                    ->label('Warehouse'),
            ])->recordActions([]);
    }
}
