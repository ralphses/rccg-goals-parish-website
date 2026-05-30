<?php

namespace App\Enums;

enum AnnouncementFrequency:string
{
    case ONCE = 'once';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case FIRST_WEEK = 'first_week';
    case SECOND_WEEK = 'second_week';
    case THIRD_WEEK = 'third_week';
    case LAST_WEEK = 'last_week';
}