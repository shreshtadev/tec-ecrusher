<?php

namespace App\Filament\Resources\Expenses\Schemas;

use App\Enums\ExpenseOpts;
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
                    ->options(collect(ExpenseOpts::cases())
                        ->mapWithKeys(fn($case) => [
                            $case->value => ucfirst(str_replace('_', ' ', $case->value)),
                        ])
                        ->toArray())
                    ->required()
                    ->native(false)
                    ->default(ExpenseOpts::Diesel->value),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
