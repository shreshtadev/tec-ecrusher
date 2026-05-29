<?php

namespace App\Filament\Resources\StockMovements\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StockMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('movement_date')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('item.material_name')
                    ->label('Item')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('warehouse.name')
                    ->label('Warehouse')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('movement_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'IN' => 'success',
                        'OUT' => 'danger',
                        'ADJUSTMENT' => 'secondary',
                        default => 'gray',
                    }),
                TextColumn::make('quantity')
                    ->label('Qty')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit_cost')
                    ->label('Unit Cost')
                    ->numeric(),
                TextColumn::make('remarks')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->remarks),
            ])
            ->filters([
                SelectFilter::make('movement_type')
                    ->options([
                        'IN' => 'Stock In',
                        'OUT' => 'Stock Out',
                        'ADJUSTMENT' => 'Adjustment',
                    ]),
            ])
            ->defaultSort('movement_date', 'desc')
            ->recordActions([]);
    }
}
