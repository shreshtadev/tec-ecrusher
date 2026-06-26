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
        $company = Company::query()
            ->lockForUpdate()
            ->findOrFail($company->id);

        $prefixColumn   = "{$type->value}_prefix";
        $sequenceColumn = "{$type->value}_sequence";
        $dateColumn     = "{$type->value}_last_reset_at";

        $today = now()->toDateString();

        if (array_key_exists($dateColumn, $company->getAttributes())) {
            // Reset daily
            $lastDate = $company->{$dateColumn};

            $nextSequence = ($lastDate === $today)
                ? $company->{$sequenceColumn} + 1
                : 1;

            $company->{$dateColumn} = $today;
        } else {
            // No reset column, just keep incrementing
            $nextSequence = $company->{$sequenceColumn} + 1;
        }

        $company->{$sequenceColumn} = $nextSequence;
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
