<?php

namespace App\Support;

class UserIdentifier
{
    public static function email(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : null;

        return $value === '' || $value === null ? null : mb_strtolower($value);
    }

    public static function phone(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : null;

        if ($value === '' || $value === null) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $value);

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (preg_match('/^0([7-9]\d{9})$/', $digits, $matches)) {
            return '+234'.$matches[1];
        }

        if (preg_match('/^234([7-9]\d{9})$/', $digits, $matches)) {
            return '+234'.$matches[1];
        }

        return null;
    }

    /**
     * Normalize values for validation while retaining invalid non-empty input so
     * the validation layer can show a useful error instead of treating it as blank.
     *
     * @return array{email: ?string, phone: ?string}
     */
    public static function forValidation(mixed $email, mixed $phone): array
    {
        $rawPhone = is_string($phone) ? trim($phone) : null;

        return [
            'email' => self::email($email),
            'phone' => self::phone($phone) ?? ($rawPhone === '' ? null : $rawPhone),
        ];
    }
}
