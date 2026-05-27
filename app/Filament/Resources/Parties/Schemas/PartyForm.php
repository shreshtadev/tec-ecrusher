<?php

namespace App\Filament\Resources\Parties\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PartyForm
{
    public static function configure(Schema $schema): Schema
    {
        $indiaCodes = [
            // States
            'AP' => 'Andhra Pradesh',
            'AR' => 'Arunachal Pradesh',
            'AS' => 'Assam',
            'BR' => 'Bihar',
            'CG' => 'Chhattisgarh',
            'GA' => 'Goa',
            'GJ' => 'Gujarat',
            'HR' => 'Haryana',
            'HP' => 'Himachal Pradesh',
            'JH' => 'Jharkhand',
            'KA' => 'Karnataka',
            'KL' => 'Kerala',
            'MP' => 'Madhya Pradesh',
            'MH' => 'Maharashtra',
            'MN' => 'Manipur',
            'ML' => 'Meghalaya',
            'MZ' => 'Mizoram',
            'NL' => 'Nagaland',
            'OD' => 'Odisha',
            'PB' => 'Punjab',
            'RJ' => 'Rajasthan',
            'SK' => 'Sikkim',
            'TN' => 'Tamil Nadu',
            'TS' => 'Telangana',
            'TR' => 'Tripura',
            'UP' => 'Uttar Pradesh',
            'UK' => 'Uttarakhand',
            'WB' => 'West Bengal',

            // Union Territories
            'AN' => 'Andaman and Nicobar Islands',
            'CH' => 'Chandigarh',
            'DD' => 'Dadra and Nagar Haveli and Daman and Diu',
            'DL' => 'Delhi',
            'JK' => 'Jammu and Kashmir',
            'LA' => 'Ladakh',
            'LD' => 'Lakshadweep',
            'PY' => 'Puducherry',
        ];
        return $schema
            ->components([
                TextInput::make('full_name')
                    ->required(),
                TextInput::make('address_line_1'),
                TextInput::make('address_line_2'),
                TextInput::make('city'),
                Select::make('state')
                    ->options($indiaCodes)
                    ->default('KA')
                    ->native(false)
                    ->searchable(),
                TextInput::make('postal_code')->maxLength(12),
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
