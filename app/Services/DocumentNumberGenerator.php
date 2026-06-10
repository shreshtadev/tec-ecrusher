<?php

namespace App\Services;

use App\Enums\DocOpts;
use App\Models\Company;

class DocumentNumberGenerator
{
    public static function generate(
        Company $company,
        DocOpts $type,
    ): string {
        // 1. Lock the record
        $company = Company::query()
            ->lockForUpdate()
            ->findOrFail($company->id);

        $prefixColumn = "{$type->value}_prefix";
        $sequenceColumn = "{$type->value}_sequence";
        $dateColumn = "{$type->value}_last_reset_at";

        $today = now()->format('Y-m-d');
        $lastDate = $company->{$dateColumn};

        // 2. Logic: Reset if the date has changed
        $nextSequence = ($lastDate === $today) ? ($company->{$sequenceColumn} + 1) : 1;

        // 3. Update the company record
        $company->{$sequenceColumn} = $nextSequence;
        $company->{$dateColumn} = $today;
        $company->save();

        return sprintf(
            '%s-%s-%s-%s-%05d',
            $company->{$prefixColumn},
            now()->format('Y'),
            now()->format('m'),
            now()->format('d'),
            $nextSequence,
        );
    }
}
