<?php

namespace App\Enums;

enum MediaUploadStatus: string
{
    case QUEUED = 'queued';
    case PROCESSING = 'processing';
    case READY = 'ready';
    case FAILED = 'failed';
}
