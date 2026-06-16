<?php

namespace App\Filament\Resources\Items\RelationManagers;

use App\Filament\Resources\Items\ItemResource;
use App\Filament\Resources\ProductionEntries\Schemas\ProductionEntryRelationForm;
use Carbon\Carbon;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ProductionEntriesRelationManager extends RelationManager
{
    protected static string $relationship = 'productionEntries';

    public function form(Schema $schema): Schema
    {
        return ProductionEntryRelationForm::configure($schema);
    }

    public function table(Table $table): Table
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
                TextColumn::make('warehouse.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('batch_no')
                    ->searchable(),
            ])
            ->headerActions([
                CreateAction::make()->label('Add Stock'),
            ]);
    }
}
