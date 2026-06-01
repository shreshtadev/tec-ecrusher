<?php

namespace App\Domains\Common\Services;

use App\Domains\Master\Models\Company;
use App\Domains\Common\Enums\DocOpts;

class DocumentNumberGenerator
{
    public static function generate(
        Company $company,
        DocOpts $type,
    ): string {

        $company = Company::query()
            ->lockForUpdate()
            ->findOrFail($company->id);

        $prefixColumn = "{$type->value}_prefix";
        $sequenceColumn = "{$type->value}_sequence";

        $nextSequence = $company->{$sequenceColumn} + 1;

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
