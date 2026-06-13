<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Enums\PaymentOpts;
use App\Models\Company;
use App\Models\Party;
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
                    ->dehydrated(false)
                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                        //
                        $totalAmount = $get('total_amount') ?? 0;
                        $discountAmount = $totalAmount * ($state / 100.0 ?? 0);
                        $grandTotal = $totalAmount - $discountAmount;
                        $set('discount_amount', round($discountAmount));
                        $set('grand_total', $grandTotal);
                    }),
                TextInput::make('discount_amount')
                    ->prefix('₹')
                    ->required()
                    ->live()
                    ->default(0)
                    ->numeric()
                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                        $totalAmount = $get('total_amount') ?? 0;
                        $discountPercentage = (($state / $totalAmount) * 100) ?? 0;
                        $grandTotal = number_format($totalAmount - $state, 3);
                        $set('grand_total', $grandTotal);
                        $set("discount_percentage", round($discountPercentage));
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
