<?php

namespace App\Filament\Resources\Parties\Pages;

use App\Filament\Resources\Parties\PartyResource;
use App\Models\Account;
use Exception;
use Filament\Resources\Pages\CreateRecord;

class CreateParty extends CreateRecord
{
    protected static string $resource = PartyResource::class;

    protected function afterCreate(): void
    {
        try {
            $defaultAccount = Account::create([
                'account_type' => 'asset',
                'account_mode' => 'bank',
                'is_active' => true,
                'party_id' => $this->record->id,
                'company_id' => $this->record->company->id,
            ]);
            logger()->info("Default account created. {$defaultAccount->title}");
        } catch (Exception $e) {
            logger()->error("Error creating default account.");
            report($e);
        }
    }
}
