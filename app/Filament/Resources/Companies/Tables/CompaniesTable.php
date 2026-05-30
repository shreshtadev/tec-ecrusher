<?php

namespace App\Filament\Resources\Companies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('legal_name')
                    ->searchable(),
                TextColumn::make('gstin')
                    ->searchable(),
                TextColumn::make('pan')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('website')
                    ->searchable(),
                TextColumn::make('state')
                    ->searchable(),
                TextColumn::make('state_code')
                    ->searchable(),
                TextColumn::make('cin')
                    ->searchable(),
                TextColumn::make('upi_id')
                    ->searchable(),
                TextColumn::make('invoice_number_format')
                    ->searchable(),
                TextColumn::make('challan_number_format')
                    ->searchable(),
                TextColumn::make('logo')
                    ->searchable(),
                TextColumn::make('bank_name')
                    ->searchable(),
                TextColumn::make('account_number')
                    ->searchable(),
                TextColumn::make('ifsc')
                    ->searchable(),
                TextColumn::make('branch')
                    ->searchable(),
                TextColumn::make('invoice_prefix')
                    ->searchable(),
                TextColumn::make('challan_prefix')
                    ->searchable(),
                TextColumn::make('authorized_signatory')
                    ->searchable(),
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
