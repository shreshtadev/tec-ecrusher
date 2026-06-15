<?php

namespace App\Filament\Resources\Accounts\Schemas;

use App\Enums\AccountMode;
use App\Enums\AccountType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('company_id')
                    ->label('Company')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()
                    ->default(fn(Select $component): string => array_key_first($component->getOptions()))
                    ->native(false),
                Select::make('party_id')
                    ->relationship('party', 'full_name')
                    ->native(false),
                TextInput::make('account_number')
                    ->default(null),
                Select::make('account_type')
                    ->options(collect(AccountType::cases())
                        ->mapWithKeys(fn($case) => [
                            $case->value => ucfirst($case->value),
                        ])
                        ->toArray())
                    ->native(false)
                    ->required()
                    ->default('asset'),
                Select::make('account_mode')
                    ->options(collect(AccountMode::cases())
                        ->mapWithKeys(fn($case) => [
                            $case->value => ucfirst($case->value),
                        ])
                        ->toArray())
                    ->native(false)
                    ->required()
                    ->default('cash'),
                TextInput::make('bank_name')
                    ->default(null),
                TextInput::make('branch_code')
                    ->default(null),
                TextInput::make('balance')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
