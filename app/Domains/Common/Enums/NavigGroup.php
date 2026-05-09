<?php

namespace App\Domains\Common\Enums;

use Filament\Support\Contracts\HasLabel;

enum NavigGroup: string implements HasLabel
{
    case MasterData = 'master_data';
    case Operation = 'operations';
    case Accounting = 'accounting';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MasterData => 'Master Data',
            self::Operation => 'Operations',
            self::Accounting => 'Accounting',
        };
    }
}
