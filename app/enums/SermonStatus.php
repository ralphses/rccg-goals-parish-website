<?php

namespace App\Enums;

enum SermonStatus: string
{
    case PUBLISHED = 'published';
    case DRAFT = 'draft';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}