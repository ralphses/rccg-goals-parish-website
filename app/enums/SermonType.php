<?php

namespace App\Enums;

enum SermonType: string
{
    case SUNDAY_SERVICE = 'sunday_service';
    case MIDWEEK_SERVICE = 'midweek_service';
    case SPECIAL_PROGRAM = 'special_program';
    case CONFERENCE = 'conference';
}
