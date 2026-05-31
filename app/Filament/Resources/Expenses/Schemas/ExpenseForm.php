<?php

namespace App\Filament\Resources\Expenses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('expenditure_date')->native(false)
                    ->placeholder('Expenditure Date')
                    ->required(),
                Select::make('party_id')
                    ->label('Party')
                    ->relationship('party', 'full_name')
                    ->native(false),
                Select::make('category')
                    ->options([
                        'Diesel' => 'Diesel',
                        'Maintenance' => 'Maintenance',
                        'Salary' => 'Salary',
                        'Electricity' => 'Electricity',
                    ])
                    ->required()->default('Diesel'),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('reference_no'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
