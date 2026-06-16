<?php

namespace App\Filament\Resources\StockIssues\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StockIssuesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('warehouse.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('issue_no')
                    ->searchable(),
                TextColumn::make('issue_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('purpose')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()->formatStateUsing(fn($state) => strtoupper($state)),
                TextColumn::make('company.name')
                    ->sortable(),
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
                ]),
            ]);
    }
}
