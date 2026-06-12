<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Enums\PaymentOpts;
use App\Models\Company;
use App\Models\Party;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('invoice_number')
                    ->hiddenOn('create')
                    ->readOnly('edit'),
                Select::make('company_id')
                    ->label('Companny')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()
                    ->default(fn() => Company::query()->value('id'))
                    ->native(false),
                Select::make('party_id')
                    ->label('Party')
                    ->required()
                    ->options(fn() => Party::pluck('full_name', 'id')->toArray())->native(false),
                TextInput::make('total_amount')
                    ->prefix('₹')
                    ->required()
                    ->default(0)
                    ->numeric(),
                TextInput::make('driver_bata')
                    ->prefix('₹')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('payment_mode')
                    ->required()
                    ->default(PaymentOpts::AC)
                    ->options(PaymentOpts::options())
                    ->native(false),
            ]);
    }
}
