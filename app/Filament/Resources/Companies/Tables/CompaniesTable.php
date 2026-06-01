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
                    ->label('Company Name')
                    ->searchable(),
                TextColumn::make('legal_name')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('gstin')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('pan')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label('Phone Number')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('website')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('state')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('state_code')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('cin')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('upi_id')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('invoice_number_format')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('challan_number_format')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('logo')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('bank_name')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('account_number')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ifsc')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('branch')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('invoice_prefix')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('challan_prefix')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('voucher_prefix')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('voucher_number_format')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('voucher_sequence')
                    ->toggleable(isToggledHiddenByDefault: true),
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
