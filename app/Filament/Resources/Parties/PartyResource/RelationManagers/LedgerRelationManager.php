<?php

namespace App\Filament\Resources\Parties\PartyResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LedgerEntriesRelationManager extends RelationManager
{
    protected static string $relationship = 'ledgerEntries';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->defaultSort('entry_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('entry_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Particulars')
                    ->searchable(),

                // Debit Column (Money Owed to Us)
                Tables\Columns\TextColumn::make('debit')
                    ->label('Debit (+)')
                    ->money('INR')
                    ->color('danger')
                    ->alignment('right')
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->label('Total Dr')),

                // Credit Column (Payments Received)
                Tables\Columns\TextColumn::make('credit')
                    ->label('Credit (-)')
                    ->money('INR')
                    ->color('success')
                    ->alignment('right')
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->label('Total Cr')),

                // Running Balance (Calculated via a dedicated service or simple sum)
                Tables\Columns\TextColumn::make('balance')
                    ->label('Balance')
                    ->money('INR')
                    ->weight('bold')
                    ->alignment('right'),
            ])
            ->filters([
                Tables\Filters\Filter::make('entry_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from'),
                        \Filament\Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('entry_date', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('entry_date', '<=', $data['until']));
                    })
            ])
            ->headerActions([
                // You can add a "Print PDF" action here later
            ]);
    }
}
