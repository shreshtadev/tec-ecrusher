<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidUpiId implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match(
            '/^[a-zA-Z0-9.\-_]{2,256}@[a-zA-Z]{2,64}$/',
            $value
        )) {
            $fail('Please enter a valid UPI ID.');
        }
    }
}
