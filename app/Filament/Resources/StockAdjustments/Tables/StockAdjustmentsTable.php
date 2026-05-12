<?php

namespace App\Filament\Resources\StockAdjustments\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StockAdjustmentsTable
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
                TextColumn::make('adjustment_type')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity_change')
                    ->label('Qty Change')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('reason')
                    ->limit(50)
                    ->tooltip(fn($record) => $record->reason),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('adjustment_type')
                    ->options([
                        'Damage' => 'Damage',
                        'Loss' => 'Loss',
                        'Correction' => 'Correction',
                        'Audit' => 'Audit',
                        'Other' => 'Other',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
