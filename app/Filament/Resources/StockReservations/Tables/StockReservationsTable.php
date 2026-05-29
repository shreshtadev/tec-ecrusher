<?php

namespace App\Filament\Resources\StockReservations\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StockReservationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('source_type')
                    ->searchable(),
                TextColumn::make('source_id')
                    ->sortable(),
                TextColumn::make('warehouse.name')
                    ->searchable(),
                TextColumn::make('item.material_name')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
