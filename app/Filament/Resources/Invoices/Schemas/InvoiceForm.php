<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Enums\PaymentOpts;
use App\Models\Company;
use App\Models\Party;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
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
                Group::make()
                    ->statePath('invoice_date')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // 1. Date Dropdown
                                DatePicker::make('invoice_day')
                                    ->label('Invoice Date')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d-m-Y') // Formats how the user sees the date in the input field
                                    ->format('Y-m-d')
                                    ->disabled(fn(string $operation) => $operation === 'edit'),

                                // 2. Dynamic Time Dropdown (Generates 12 hours with exact minute increments)
                                TimePicker::make('invoice_time')
                                    ->label('Time')
                                    ->seconds(false)
                                    ->displayFormat('h:i A')->disabled(fn(string $operation) => $operation === 'edit'),
                            ])
                    ])
                    // Formats default selections for the Create form using the current time
                    ->default(function () {
                        $now = now();
                        return [
                            'invoice_day' => $now->format('Y-m-d'),
                            'invoice_time' => $now->format('h:i A'),
                        ];
                    })
                    // Combines the dropdown selections into a standard database timestamp string
                    ->dehydrateStateUsing(function ($state) {
                        $date = $state['invoice_day'] ?? null;
                        $time = $state['invoice_time'] ?? null;

                        if ($date && $time) {
                            return Carbon::parse("{$date} {$time}")->format('Y-m-d H:i:s');
                        }

                        return null;
                    })
                    // Safely reads the database record and splits it into the fields during Edit mode
                    ->afterStateHydrated(function ($component, $state) {
                        if (is_string($state)) {
                            $carbonDate = Carbon::parse($state);

                            $component->state([
                                'invoice_day' => $carbonDate->format('Y-m-d'),
                                'invoice_time' => $carbonDate->format('h:i'), // FIXED: Captures exact minutes (e.g., "06:30")
                                'invoice_ampm' => $carbonDate->format('A'),
                            ]);
                        }
                    }),
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
