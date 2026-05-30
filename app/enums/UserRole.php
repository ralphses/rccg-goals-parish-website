<?php

namespace App\enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case EDITOR = 'editor';
    case PASTOR = 'pastor';
    case MEDIA = 'media';
    case MEMBER = 'member';
}
