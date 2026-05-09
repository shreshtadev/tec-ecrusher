<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Domains\Master\Models\Party;
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
                    ->required(),
                Select::make('party.full_name')
                    ->required()
                    ->options(fn() => Party::pluck('full_name', 'id')->toArray()),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('driver_bata')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('payment_mode')
                    ->required()
                    ->default('Credit')
                    ->options([
                        'Credit' => 'Credit',
                        'Cash' => 'Cash',
                        'UPI' => 'UPI',
                        'Cheque' => 'Cheque',
                        'NEFT' => 'NEFT',
                        'RTGS' => 'RTGS',
                    ]),
            ]);
    }
}
