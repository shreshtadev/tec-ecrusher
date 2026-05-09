<?php

namespace App\Filament\Resources\Vouchers\Schemas;

use App\Domains\Operations\Models\Invoice;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class VoucherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Voucher Details')
                    ->schema([
                        TextInput::make('voucher_no')
                            ->default(fn() => 'VCH-' . date('Ymd-His'))
                            ->readonly()
                            ->required(),
                        DatePicker::make('voucher_date')
                            ->default(now())
                            ->required(),
                        Select::make('voucher_type')
                            ->options(['Payment' => 'Payment (Out)', 'Receipt' => 'Receipt (In)'])
                            ->required()
                            ->native(false),
                    ])->columns(3),

                Section::make('Transaction')
                    ->schema([
                        Select::make('party_id')
                            ->relationship('party', 'full_name')
                            ->searchable()
                            ->preload()
                            ->live() // Watch for changes to filter invoices
                            ->required(),

                        // Adjustment logic: Filter invoices by the selected party
                        Select::make('reference_invoice_id')
                            ->label('Against Invoice (Optional)')
                            ->options(
                                fn(Get $get) =>
                                Invoice::where('party_id', $get('party_id'))
                                    ->pluck('invoice_number', 'id')
                            )
                            ->searchable()
                            ->placeholder('Select invoice to adjust'),

                        TextInput::make('amount')
                            ->numeric()
                            ->prefix('₹')
                            ->required(),

                        Select::make('payment_mode')
                            ->options(['Cash' => 'Cash', 'Bank' => 'Bank Transfer', 'Cheque' => 'Cheque'])
                            ->default('Cash'),
                    ])->columns(2),

                Textarea::make('remarks')->columnSpanFull(),
            ]);
    }
}
