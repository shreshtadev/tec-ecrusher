<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Enums\PaymentOpts;
use App\Models\Company;
use App\Models\Party;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                DateTimePicker::make('invoice_date')
                    ->required()
                    ->placeholder("Please select a correct date.")
                    ->seconds(false)
                    ->default(now())
                    ->displayFormat('d-M-Y H:i A')
                    ->native(false),
                Select::make('company_id')
                    ->label('Company')
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
                    ->searchable()
                    ->options(fn() => Party::pluck('full_name', 'id')->toArray())->native(false),
                TextInput::make('total_amount')
                    ->prefix('₹')
                    ->required()
                    ->default(0)
                    ->live()
                    ->numeric()->afterStateUpdated(function ($state, Get $get, Set $set) {
                        //
                        $discountAmount = $get('discount_amount') ?? 0;
                        $set('grand_total', ($state ?? 0) - $discountAmount);
                    }),
                TextInput::make('discount_percentage')
                    ->prefix('%')
                    ->live()
                    ->default(0)
                    ->maxValue(100)
                    ->numeric()
                    ->dehydrated(false) // Safely kept!
                    // FIX 1: Added missing page load computation
                    ->afterStateHydrated(function (TextInput $component, Get $get) {
                        $totalAmount = (float)($get('total_amount') ?? 0);
                        $discountAmount = (float)($get('discount_amount') ?? 0);
                        $discountPercentage = $totalAmount > 0 ? (($discountAmount / $totalAmount) * 100) : 0;

                        $component->state(round($discountPercentage, 2));
                    })
                    // FIX 2: Added missing $state variable into the closure arguments list
                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                        $totalAmount = (float)($get('total_amount') ?? 0);

                        $discountAmount = $totalAmount * ((float)($state ?? 0) / 100);
                        $grandTotal = $totalAmount - $discountAmount;

                        $set('discount_amount', round($discountAmount, 2));
                        $set('grand_total', $grandTotal);
                    }),

                TextInput::make('discount_amount')
                    ->prefix('₹')
                    ->required()
                    ->live()
                    ->default(0)
                    ->numeric()
                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                        $totalAmount = (float)($get('total_amount') ?? 0);
                        $discountAmount = (float)($state ?? 0);

                        // Prevent Division by Zero error if total_amount is zero or empty
                        $discountPercentage = $totalAmount > 0 ? (($discountAmount / $totalAmount) * 100) : 0;
                        $grandTotal = $totalAmount - $discountAmount;

                        $set("discount_percentage", round($discountPercentage, 2));
                        $set('grand_total', $grandTotal);
                    }),
                TextInput::make('grand_total')
                    ->prefix('₹')
                    ->required()
                    ->live()
                    ->readOnly()
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
