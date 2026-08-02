<?php

namespace App\Filament\Resources\Parties\Pages;

use App\Filament\Resources\Parties\PartyResource;
use App\Models\Account;
use Exception;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateParty extends CreateRecord
{
    protected static string $resource = PartyResource::class;

    protected function afterCreate(): void
    {
        $party = $this->record;
        try {
            DB::transaction(function () use ($party) {
                $defaultAccount = Account::create([
                    'account_type' => 'asset',
                    'account_mode' => 'bank',
                    'is_active' => true,
                    'party_id' => $party->id,
                    'company_id' => $party->company->id,
                ]);
                logger()->info("Default account created. {$defaultAccount->title}");
            });
        } catch (Exception $e) {
            logger()->error("Error creating default account.");
            report($e);
        }
    }
}
