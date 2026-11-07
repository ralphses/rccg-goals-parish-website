<?php

namespace App\Rules;

use App\Support\UserIdentifier;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NigerianPhone implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value !== null && $value !== '' && UserIdentifier::phone($value) === null) {
            $fail('The :attribute must be a valid Nigerian mobile number.');
        }
    }
}
