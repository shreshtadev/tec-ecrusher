<?php

namespace App\Filament\Resources\ProductionEntries\Tables;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ProductionEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('production_entry_date')
                    ->sortable()
                    ->state(function ($record): HtmlString|string|null {
                        if (blank($record->production_entry_date)) {
                            // Return custom HTML string with Tailwind classes for the fallback
                            return new HtmlString(
                                '<span class="text-xs font-semibold uppercase tracking-wider text-amber-700 bg-amber-100 dark:bg-amber-900/30 px-2 py-1 rounded">Pending</span>'
                            );
                        }

                        // Manually format the date since we overrode the default state lifecycle
                        return Carbon::parse($record->production_entry_date)->format('M d, Y');
                    }),
                TextColumn::make('item.material_name')
                    ->sortable(),
                TextColumn::make('item.unit'),
                TextColumn::make('warehouse.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('batch_no')
                    ->searchable(),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
