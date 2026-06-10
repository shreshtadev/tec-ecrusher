<?php

namespace App\Filament\Resources\Drivers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DriverForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('party_id')
                    ->relationship('party', 'full_name')->native(false),
                TextInput::make('full_name')
                    ->required(),
                TextInput::make('phone_number')
                    ->tel()->required(),
            ]);
    }
}
