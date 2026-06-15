<?php

namespace App\Filament\Resources\Parties\Schemas;

use App\Enums\IndianStates;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PartyForm
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
                TextInput::make('full_name')
                    ->required(),
                TextInput::make('address_line_1'),
                TextInput::make('address_line_2'),
                TextInput::make('city'),
                Select::make('state')
                    ->options(IndianStates::selectStateOptions())
                    ->default('KA')
                    ->searchable()
                    ->native(false)
                    ->required(),
                TextInput::make('postal_code')->maxLength(12),
                TextInput::make('contact_number')->tel(),
                Select::make('party_type')
                    ->options([
                        'Customer' => 'Customer',
                        'Supplier' => 'Supplier',
                        'Employee' => 'Employee',
                        'Other' => 'Other',
                    ])
                    ->required()
                    ->native(false),
            ]);
    }
}
