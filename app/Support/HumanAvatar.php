<?php

namespace App\Support;

class HumanAvatar
{
    public const RELATIVE_PATH = 'assets/images/resources/default-avatar-human.png';

    public static function url(): string
    {
        return asset(self::RELATIVE_PATH);
    }
}
