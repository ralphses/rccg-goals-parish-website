<?php

namespace App\Support;

class HumanAvatar
{
    public const RELATIVE_PATH = 'default-avatar-human.png';

    public static function url(): string
    {
        return asset(self::RELATIVE_PATH);
    }
}
