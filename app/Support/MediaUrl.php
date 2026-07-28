<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class MediaUrl
{
    public static function isAbsolute(?string $value): bool
    {
        return !empty($value) && preg_match('/^https?:\/\//i', $value) === 1;
    }

    public static function toPublicUrl(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (self::isAbsolute($value)) {
            return $value;
        }

        return url(Storage::url($value));
    }

    public static function toStoragePath(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (!self::isAbsolute($value)) {
            return ltrim($value, '/');
        }

        $path = parse_url($value, PHP_URL_PATH);

        if (!$path) {
            return null;
        }

        $storagePrefix = '/storage/';
        $position = strpos($path, $storagePrefix);

        if ($position === false) {
            return null;
        }

        return ltrim(substr($path, $position + strlen($storagePrefix)), '/');
    }
}
