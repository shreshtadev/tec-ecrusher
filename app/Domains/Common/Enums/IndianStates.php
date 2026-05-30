<?php

namespace App\Domains\Common\Enums;

class IndianStates
{
    public static function options(): array
    {
        return [
            '01' => ['name' => 'Jammu and Kashmir', 'abbr' => 'JK'],
            '02' => ['name' => 'Himachal Pradesh', 'abbr' => 'HP'],
            '03' => ['name' => 'Punjab', 'abbr' => 'PB'],
            '04' => ['name' => 'Chandigarh', 'abbr' => 'CH'],
            '05' => ['name' => 'Uttarakhand', 'abbr' => 'UK'],
            '06' => ['name' => 'Haryana', 'abbr' => 'HR'],
            '07' => ['name' => 'Delhi', 'abbr' => 'DL'],
            '08' => ['name' => 'Rajasthan', 'abbr' => 'RJ'],
            '09' => ['name' => 'Uttar Pradesh', 'abbr' => 'UP'],
            '10' => ['name' => 'Bihar', 'abbr' => 'BR'],
            '11' => ['name' => 'Sikkim', 'abbr' => 'SK'],
            '12' => ['name' => 'Arunachal Pradesh', 'abbr' => 'AR'],
            '13' => ['name' => 'Nagaland', 'abbr' => 'NL'],
            '14' => ['name' => 'Manipur', 'abbr' => 'MN'],
            '15' => ['name' => 'Mizoram', 'abbr' => 'MZ'],
            '16' => ['name' => 'Tripura', 'abbr' => 'TR'],
            '17' => ['name' => 'Meghalaya', 'abbr' => 'ML'],
            '18' => ['name' => 'Assam', 'abbr' => 'AS'],
            '19' => ['name' => 'West Bengal', 'abbr' => 'WB'],
            '20' => ['name' => 'Jharkhand', 'abbr' => 'JH'],
            '21' => ['name' => 'Odisha', 'abbr' => 'OD'],
            '22' => ['name' => 'Chhattisgarh', 'abbr' => 'CG'],
            '23' => ['name' => 'Madhya Pradesh', 'abbr' => 'MP'],
            '24' => ['name' => 'Gujarat', 'abbr' => 'GJ'],
            '26' => ['name' => 'Dadra and Nagar Haveli and Daman and Diu', 'abbr' => 'DNHDD'],
            '27' => ['name' => 'Maharashtra', 'abbr' => 'MH'],
            '29' => ['name' => 'Karnataka', 'abbr' => 'KA'],
            '30' => ['name' => 'Goa', 'abbr' => 'GA'],
            '31' => ['name' => 'Lakshadweep', 'abbr' => 'LD'],
            '32' => ['name' => 'Kerala', 'abbr' => 'KL'],
            '33' => ['name' => 'Tamil Nadu', 'abbr' => 'TN'],
            '34' => ['name' => 'Puducherry', 'abbr' => 'PY'],
            '35' => ['name' => 'Andaman and Nicobar Islands', 'abbr' => 'AN'],
            '36' => ['name' => 'Telangana', 'abbr' => 'TS'],
            '37' => ['name' => 'Andhra Pradesh', 'abbr' => 'AP'],
            '38' => ['name' => 'Ladakh', 'abbr' => 'LA'],
        ];
    }

    public static function stateOptions(): array
    {
        return collect(self::options())
            ->mapWithKeys(
                fn ($state, $code) => [$code => $state['name']]
            )
            ->toArray();
    }

    public static function selectStateOptions(): array
    {
        return collect(self::options())
            ->mapWithKeys(
                fn ($state) => [$state['abbr'] => $state['name']]
            )
            ->toArray();
    }

    public static function name(string $code): ?string
    {
        return self::options()[$code]['name'] ?? null;
    }

    public static function abbreviation(string $code): ?string
    {
        return self::options()[$code]['abbr'] ?? null;
    }
}
