<?php

namespace App\Filament\Resources\Drivers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DriverRelationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->inlineLabel("New Driver")
            ->components([
                TextInput::make('full_name')
                    ->required(),
                TextInput::make('phone_number')
                    ->tel()->required(),
            ]);
    }
}
