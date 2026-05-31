<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Domains\Common\Enums\PaymentOpts;
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
                Select::make('company_id')
                    ->label('Companny')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()
                    ->native(false),
                Select::make('party_id')
                    ->label('Party')
                    ->required()
                    ->options(fn() => Party::pluck('full_name', 'id')->toArray()),
                TextInput::make('total_amount')
                    ->prefix('₹')
                    ->required()
                    ->numeric(),
                TextInput::make('driver_bata')
                    ->prefix('₹')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('payment_mode')
                    ->required()
                    ->default('Credit')
                    ->options(PaymentOpts::options()),
            ]);
    }
}
