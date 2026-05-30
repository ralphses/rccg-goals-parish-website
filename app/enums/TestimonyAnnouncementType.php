<?php

namespace App\Enums;

enum TestimonyAnnouncementType:string
{
    case TEXT = 'text';
    case AUDIO = 'audio';
    case VIDEO = 'video';
}