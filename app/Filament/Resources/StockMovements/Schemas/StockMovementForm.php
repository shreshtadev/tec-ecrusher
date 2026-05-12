<?php

namespace App\Filament\Resources\StockMovements\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StockMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('readonly')
                    ->content('Stock movements are read-only audit records.'),
            ]);
    }
}
