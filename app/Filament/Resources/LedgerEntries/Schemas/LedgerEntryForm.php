<?php

namespace App\Filament\Resources\LedgerEntries\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LedgerEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('entry_date')
                    ->required(),
                Select::make('party_id')
                    ->required()
                    ->relationship('party', 'full_name'),
                TextInput::make('recordable_type')
                    ->required(),
                TextInput::make('recordable_id')
                    ->required()->numeric(),
                TextInput::make('description')
                    ->required(),
                TextInput::make('debit')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('credit')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('balance')
                    ->required()
                    ->numeric(),
            ]);
    }
}
