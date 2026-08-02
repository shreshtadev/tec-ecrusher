<?php

namespace App\Filament\Resources\Parties\Pages;

use App\Filament\Resources\Parties\PartyResource;
use App\Models\Account;
use Exception;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateParty extends CreateRecord
{
    protected static string $resource = PartyResource::class;

    protected function afterCreate(): void
    {
        $party = $this->record;

        try {
            // Safely fetch company ID directly from the attribute or fallback safely
            $companyId = $party->company_id ?? ($party->company?->id ?? null);

            if (!$companyId) {
                Log::warning("Could not resolve company_id for Party ID: {$party->id}");
                return;
            }

            Account::create([
                'account_type' => 'asset',
                'account_mode' => 'bank',
                'is_active'    => true,
                'party_id'     => $party->id,
                'company_id'   => $companyId,
            ]);
        } catch (Throwable $e) {
            // Changed from Exception to Throwable to catch fatal PHP errors
            Log::error("Error creating default account for Party ID: {$party->id}. Message: " . $e->getMessage());
            report($e);
        }
    }
}
