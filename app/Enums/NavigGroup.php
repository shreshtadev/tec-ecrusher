<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum NavigGroup: string implements HasLabel
{
    case MasterData = 'master_data';
    case Operation = 'operations';
    case Accounting = 'accounting';
    case Inventory = 'inventory';
    case Reports = 'reports';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MasterData => 'Master Data',
            self::Operation => 'Operations',
            self::Accounting => 'Accounting',
            self::Inventory => 'Inventory',
            self::Reports => 'Reports',
        };
    }
}
