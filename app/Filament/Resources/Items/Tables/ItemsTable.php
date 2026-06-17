<?php

namespace App\Filament\Resources\Items\Tables;

use App\Filament\Resources\ProductionEntries\Schemas\ProductionEntryRelationForm;
use App\Models\StockLevel;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

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
                TextColumn::make('available_qty')
                    ->label('Available Quantity')
                    ->getStateUsing(function ($record) {
                        $stockLevel = StockLevel::where('item_id', $record->id)->first();

                        // Null check to prevent errors if no stock record exists
                        return $stockLevel ? $stockLevel->available_qty : 0;
                    }),
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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('bulk_edit_production')
                        ->label('Bulk Edit Stock')
                        ->icon('heroicon-o-pencil-square')
                        ->modalHeading('Batch Edit Entries')
                        // 1. Initialize a blank Schema container, configure it, and extract components
                        ->schema(
                            ProductionEntryRelationForm::configure(new Schema())
                                ->getComponents()
                        )
                        // 2. Process the mass updates using the user input
                        ->action(function (Collection $records, array $data): void {
                            foreach ($records as $record) {
                                // Ensure to perform null checks or target relations matching your database design
                                $record->productionEntries()->updateOrCreate(
                                    ['item_id' => $record->id],
                                    [
                                        'production_entry_date' => $data['production_entry_date'],
                                        'warehouse_id'          => $data['warehouse_id'],
                                        'quantity'              => $data['quantity'],
                                        'batch_no'              => $data['batch_no'],
                                    ]
                                );
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
