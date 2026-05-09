<?php

namespace App\Filament\Resources\Parties\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PartyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('full_name')
                    ->required(),
                TextInput::make('address_line_1'),
                TextInput::make('address_line_2'),
                TextInput::make('city'),
                TextInput::make('state')
                    ->required()
                    ->default('KA'),
                TextInput::make('postal_code'),
                TextInput::make('contact_number')->tel(),
                Select::make('party_type')
                    ->options([
                        'Customer' => 'Customer',
                        'Supplier' => 'Supplier',
                        'Other' => 'Other',
                    ])
                    ->required()
                    ->native(false),
            ]);
    }
}
