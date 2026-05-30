<?php

namespace App\Filament\Resources\Items\Tables;

use App\Filament\Resources\ProductionEntries\ProductionEntryResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('material_name')
                    ->searchable(),
                TextColumn::make('price_per_unit')
                    ->money('INR')->alignment('left'),
                TextColumn::make('unit'),
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
            ->recordActions([

                Action::make('production')
                    ->label('Add Stock')
                    ->icon('heroicon-o-plus')
                    ->color('info')
                    ->url(fn ($record) => ProductionEntryResource::getUrl('create', [
                        'item_id' => $record->id,
                    ])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
