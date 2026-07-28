<?php

namespace App\enums;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case CREATED = 'created';
}
