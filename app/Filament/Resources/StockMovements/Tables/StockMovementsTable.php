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
                TextColumn::make('created_at')
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
                    ->color(fn($state) => match ($state) {
                        'IN' => 'success',
                        'OUT' => 'danger',
                        'RESERVE' => 'warning',
                        'UNRESERVE' => 'info',
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
                TextColumn::make('notes')
                    ->limit(50)
                    ->tooltip(fn($record) => $record->notes),
            ])
            ->filters([
                SelectFilter::make('movement_type')
                    ->options([
                        'IN' => 'Stock In',
                        'OUT' => 'Stock Out',
                        'RESERVE' => 'Reserve',
                        'UNRESERVE' => 'Unreserve',
                        'ADJUSTMENT' => 'Adjustment',
                    ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
