<?php

namespace App\Filament\Resources\Vouchers\Schemas;

use App\Enums\IndianStates;
use App\Enums\PaymentOpts;
use App\Enums\VoucherOpts;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Party;
use App\Models\Voucher;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

use function Laravel\Prompts\title;

class VoucherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('voucher_no')
                            ->label('Voucher #')
                            ->readonly('edit'),
                    ])->hiddenOn('create'),
                Section::make('Voucher Details')
                    ->schema([
                        DatePicker::make('voucher_date')
                            ->default(now())
                            ->required(),
                        Select::make('voucher_type')
                            ->disabled()
                            ->dehydrated()
                            ->placeholder('Select party first')
                            ->options(function () {
                                $opts = VoucherOpts::options();

                                foreach ($opts as $key => $label) {
                                    if ($key === VoucherOpts::PAYMENT) {
                                        $opts[$key] = 'Payment (Out)';
                                    } elseif ($key === VoucherOpts::RECEIPT) {
                                        $opts[$key] = 'Receipt (In)';
                                    } elseif ($key === VoucherOpts::JOURNAL) {
                                        $opts[$key] = 'Journal Entry';
                                    } elseif ($key === VoucherOpts::CREDIT_NOTE) {
                                        $opts[$key] = 'Credit Note';
                                    } elseif ($key === VoucherOpts::DEBIT_NOTE) {
                                        $opts[$key] = 'Debit Note';
                                    } else {
                                        $opts[$key] = $label;
                                    }
                                }

                                return $opts;
                            })
                            ->required()
                            ->live()
                            ->native(false),
                        Select::make('company_id')
                            ->relationship(name: 'company', titleAttribute: 'name')
                            ->default(fn() => Company::query()->value('id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                    ])->columns(2),

                Section::make('Transaction')
                    ->schema([
                        Select::make('party_id')
                            ->relationship(name: 'party', titleAttribute: 'full_name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $partyId = $get('party_id');
                                $foundParty = Party::where('id', $partyId)->first();
                                $partyType = $foundParty?->party_type;

                                if ($partyType === 'Customer') {
                                    $set('voucher_type', VoucherOpts::RECEIPT);
                                } elseif ($partyType === 'Supplier' || $partyType === 'Employee') {
                                    $set('voucher_type', VoucherOpts::PAYMENT);
                                }
                            })
                            ->createOptionForm([
                                TextInput::make('full_name')
                                    ->required(),
                                Select::make('state')
                                    ->options(IndianStates::selectStateOptions())
                                    ->default('KA')
                                    ->searchable()
                                    ->native(false)
                                    ->required(),
                                TextInput::make('contact_number')->tel()->required(),
                                Select::make('party_type')
                                    ->options([
                                        'Customer' => 'Customer',
                                        'Supplier' => 'Supplier',
                                        'Employee' => 'Employee',
                                        'Other' => 'Other',
                                    ])
                                    ->required()
                                    ->native(false),
                            ])
                            ->required(),

                        // Adjustment logic: Filter invoices by the selected party
                        Select::make('invoice_id')
                            ->label('Against Invoice (Optional)')
                            ->options(
                                fn(Get $get) => Invoice::where('party_id', $get('party_id'))
                                    ->pluck('invoice_number', 'id')
                            )
                            ->searchable()
                            ->live()
                            ->placeholder('Select invoice to adjust'),

                        TextInput::make('amount')
                            ->numeric()
                            ->prefix('₹')
                            ->helperText(function (Get $get) {
                                $selectedInvoiceId = $get('invoice_id');

                                if (! $selectedInvoiceId) {
                                    return null;
                                }

                                $invoice = Invoice::find($selectedInvoiceId);

                                if (! $invoice) {
                                    return null;
                                }

                                $voucherTotal = Voucher::where('invoice_id', $selectedInvoiceId)
                                    ->sum('amount');

                                $totalAmount = $invoice->total_amount + $invoice->driver_bata;

                                $balance = $totalAmount - $voucherTotal;
                                $helperText = "Invoice Total: <strong>₹{$totalAmount}</strong><br>" .
                                    "Already Adjusted: <strong>₹{$voucherTotal}</strong><br>" .
                                    "Balance: <strong>₹{$balance}</strong>";

                                return new HtmlString($helperText);
                            })
                            ->required(),

                        Select::make('payment_mode')
                            ->options(PaymentOpts::options())->default(PaymentOpts::AC)->native(false),
                    ])->columns(2),
                Section::make('Additional Information')
                    ->schema([
                        Select::make('from_account_id')
                            ->label('From Account')
                            ->relationship(
                                name: 'fromAccount',
                                titleAttribute: 'title',
                                modifyQueryUsing: function ($query, Get $get) {
                                    $partyId = $get('party_id');
                                    $baseFilter = $query->where('id', '!=', $get('to_account_id'));
                                    if (!blank($partyId)) {
                                        return $baseFilter->where('party_id', '=', $partyId);
                                    }
                                    return $baseFilter;
                                }
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(fn(Get $get): bool => in_array(
                                $get('voucher_type'),
                                [VoucherOpts::PAYMENT, VoucherOpts::RECEIPT]
                            ) && $get('payment_mode') !== PaymentOpts::CASH)
                            ->different('to_account_id', 'From Account must be different from To Account')->disabled(fn(Get $get) => blank($get('party_id')))->dehydrated(),
                        Select::make('to_account_id')
                            ->label('To Account')
                            ->relationship(
                                name: 'toAccount',
                                titleAttribute: 'title',
                                modifyQueryUsing: function ($query, Get $get) {
                                    $partyId = $get('party_id');
                                    $baseFilter = $query->where('id', '!=', $get('from_account_id'));
                                    if (!blank($partyId)) {
                                        return $baseFilter->whereNull('party_id')->orWhere('party_id', '!=', $partyId);
                                    }
                                    return $baseFilter;
                                }
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(fn(Get $get): bool => in_array(
                                $get('voucher_type'),
                                [VoucherOpts::PAYMENT, VoucherOpts::RECEIPT]
                            ) && $get('payment_mode') !== PaymentOpts::CASH)
                            ->different('from_account_id', 'To Account must be different from From Account'),
                    ]),
                Textarea::make('remarks')->columnSpanFull(),
            ]);
    }
}
