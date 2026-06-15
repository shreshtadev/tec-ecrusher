<?php

namespace App\Filament\Resources\Companies\Schemas;

use App\Enums\IndianStates;
use App\Rules\ValidUpiId;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Company Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(150),

                        TextInput::make('legal_name')
                            ->maxLength(200),

                        TextInput::make('gstin')
                            ->label('GSTIN')
                            ->maxLength(15)
                            ->regex('/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[A-Z0-9]{1}Z[A-Z0-9]{1}$/')
                            ->live()
                            ->afterStateUpdated(fn($state, $set) => $set('gstin', strtoupper($state))),

                        TextInput::make('pan')
                            ->label('PAN')
                            ->maxLength(10)
                            ->regex('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/')
                            ->live()
                            ->afterStateUpdated(fn($state, $set) => $set('pan', strtoupper($state))),

                        TextInput::make('cin')
                            ->label('CIN')
                            ->maxLength(21),

                        Textarea::make('address')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Contact Details')
                    ->schema([
                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(11),

                        TextInput::make('email')
                            ->email()
                            ->maxLength(150),

                        TextInput::make('website')
                            ->url()
                            ->maxLength(150),

                        TextInput::make('upi_id')
                            ->label('UPI ID')
                            ->maxLength(100)->rules([new ValidUpiId]),
                    ])
                    ->columns(2),

                Section::make('Location')
                    ->schema([
                        Select::make('state')
                            ->options(
                                collect(IndianStates::options())
                                    ->mapWithKeys(
                                        fn($state) => [$state['name'] => $state['name']]
                                    )
                                    ->toArray()
                            )
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {

                                $code = collect(IndianStates::options())
                                    ->search(
                                        fn($item) => $item['name'] === $state
                                    );

                                $set('state_code', $code);
                            }),

                        TextInput::make('state_code')
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(2),

                Section::make('Branding')
                    ->schema([
                        FileUpload::make('logo')
                            ->image()
                            ->imageEditor()
                            ->directory('company-logos')
                            ->columnSpanFull(),
                    ]),

                Section::make('Bank Details')
                    ->schema([
                        TextInput::make('bank_name')
                            ->maxLength(100),

                        TextInput::make('account_number')
                            ->maxLength(50),

                        TextInput::make('ifsc')
                            ->label('IFSC')
                            ->maxLength(11)
                            ->live()
                            ->afterStateUpdated(fn($state, $set) => $set('ifsc', strtoupper($state))),

                        TextInput::make('branch')
                            ->maxLength(100),
                    ])
                    ->columns(2),

                Section::make('Document Numbering')
                    ->schema([
                        TextInput::make('invoice_prefix')
                            ->required()
                            ->default('INV')
                            ->maxLength(5),

                        TextInput::make('challan_prefix')
                            ->label('Tripsheet Prefix')
                            ->required()
                            ->default('CHL')
                            ->maxLength(5),
                        TextInput::make('voucher_prefix')
                            ->label('Voucher Prefix')
                            ->required()
                            ->default('VCH')
                            ->maxLength(5),
                        TextInput::make('company_account_prefix')
                            ->label('Company Account Prefix')
                            ->required()
                            ->default('CAC')
                            ->maxLength(5),
                        TextInput::make('party_account_prefix')
                            ->required()
                            ->default('PAC')
                            ->maxLength(5),

                        TextInput::make('invoice_number_format')
                            ->helperText('Example: {PREFIX}/{FY}/{NUMBER}')
                            ->placeholder('{PREFIX}/{FY}/{NUMBER}')
                            ->hidden()
                            ->maxLength(50),
                        TextInput::make('voucher_number_format')
                            ->helperText('Example: {PREFIX}/{FY}/{NUMBER}')
                            ->placeholder('{PREFIX}/{FY}/{NUMBER}')
                            ->hidden()
                            ->maxLength(50),

                        TextInput::make('challan_number_format')
                            ->label('Tripsheet Number Format')
                            ->helperText('Example: {PREFIX}/{FY}/{NUMBER}')
                            ->placeholder('{PREFIX}/{FY}/{NUMBER}')
                            ->hidden()
                            ->maxLength(50),
                        TextInput::make('challan_sequence')
                            ->label('Current Tripsheet Number')
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('invoice_sequence')
                            ->label('Current Invoice Number')
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('voucher_sequence')
                            ->label('Current Voucher Number')
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('party_account_sequence')
                            ->label('Current Party Account Number')
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('company_account_sequence')
                            ->label('Current Company Account Number')
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->columns(2),

                Section::make('Invoice Settings')
                    ->schema([
                        TextInput::make('authorized_signatory')
                            ->maxLength(100),

                        Textarea::make('invoice_terms')
                            ->rows(4)
                            ->columnSpanFull(),

                        Textarea::make('invoice_declaration')
                            ->rows(4)
                            ->columnSpanFull(),

                        Textarea::make('invoice_footer')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
