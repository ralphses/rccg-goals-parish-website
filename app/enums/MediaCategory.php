<?php

namespace App\enums;

enum MediaCategory: string
{
    case CHURCH_GALLERY = 'church_gallery';
    case TESTIMONY = 'testimony';
    case PROJECT = 'project';
    case SERMON = 'sermon';
}
