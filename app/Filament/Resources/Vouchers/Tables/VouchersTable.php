<?php

namespace App\Filament\Resources\Vouchers\Tables;

use App\Domains\Accounting\Models\Voucher;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class VouchersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('voucher_no')
                    ->label('Voucher #')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('voucher_date')
                    ->label('Voucher Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('party.full_name')
                    ->label('Party Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('voucher_type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Receipt' => 'success', // Green for incoming money
                        'Payment' => 'danger',  // Red for outgoing money
                    }),
                TextColumn::make('amount')
                    ->money('INR')
                    ->alignment('right')
                    ->summarize(Sum::make()->money('INR')),
                TextColumn::make('payment_mode')
                    ->label('Mode')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('invoice.invoice_number')
                    ->label('Adjusted Invoice')
                    ->placeholder('Direct Entry')
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
                SelectFilter::make('voucher_type')
                    ->options([
                        'Receipt' => 'Receipts',
                        'Payment' => 'Payments',
                    ]),
                SelectFilter::make('payment_mode')
                    ->options([
                        'Cash' => 'Cash',
                        'Bank' => 'Bank',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('print')
                    ->label('Print Voucher')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn(Voucher $record) => route('print.voucher', $record))
                    ->openUrlInNewTab(),
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
