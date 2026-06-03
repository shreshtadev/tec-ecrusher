<?php

namespace App\Filament\Resources\Vouchers\Schemas;

use App\Domains\Accounting\Models\Voucher;
use App\Domains\Common\Enums\IndianStates;
use App\Domains\Common\Enums\PaymentOpts;
use App\Domains\Common\Enums\VoucherOpts;
use App\Domains\Master\Models\Company;
use App\Domains\Operations\Models\Invoice;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

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
                            ->options(function () {
                                $opts = VoucherOpts::options();

                                foreach ($opts as $key => $label) {
                                    if ($key === VoucherOpts::PAYMENT) {
                                        $opts[$key] = 'Payment (Out)';
                                    } elseif ($key === VoucherOpts::RECEIPT) {
                                        $opts[$key] = 'Receipt (In)';
                                    } else {
                                        $opts[$key] = $label;
                                    }
                                }

                                return $opts;
                            })
                            ->required()
                            ->native(false),
                        Select::make('company_id')
                            ->relationship('company', 'name')
                            ->default(fn() => Company::query()->value('id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                    ])->columns(2),

                Section::make('Transaction')
                    ->schema([
                        Select::make('party_id')
                            ->relationship('party', 'full_name')
                            ->searchable()
                            ->preload()
                            ->live()
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
                        Select::make('reference_invoice_id')
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
                                $selectedInvoiceId = $get('reference_invoice_id');

                                if (! $selectedInvoiceId) {
                                    return null;
                                }

                                $invoice = Invoice::find($selectedInvoiceId);

                                if (! $invoice) {
                                    return null;
                                }

                                $voucherTotal = Voucher::where('reference_invoice_id', $selectedInvoiceId)
                                    ->sum('amount');

                                $balance = $invoice->total_amount - $voucherTotal;

                                return sprintf(
                                    'Invoice Amount: ₹%s • Applied Vouchers: ₹%s • Balance: ₹%s',
                                    number_format($invoice->total_amount, 2),
                                    number_format($voucherTotal, 2),
                                    number_format($balance, 2)
                                );
                            })
                            ->required(),

                        Select::make('payment_mode')
                            ->options(PaymentOpts::options())->default(PaymentOpts::AC)->native(false),
                    ])->columns(2),

                Textarea::make('remarks')->columnSpanFull(),
            ]);
    }
}
