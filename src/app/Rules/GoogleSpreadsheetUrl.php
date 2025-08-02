<?php

namespace App\Rules;

use App\Actions\ValidateGoogleSpreadsheetUrl;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class GoogleSpreadsheetUrl implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!ValidateGoogleSpreadsheetUrl::validate($value)) {
            $fail('The :attribute should be the URL address Google Spreadsheet');
        }
    }
}
