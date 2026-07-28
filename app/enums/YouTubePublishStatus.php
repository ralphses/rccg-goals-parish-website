<?php

namespace App\Enums;

enum YouTubePublishStatus: string
{
    case NOT_REQUESTED = 'not_requested';
    case QUEUED = 'queued';
    case UPLOADING = 'uploading';
    case UPLOADED_PRIVATE = 'uploaded_private';
    case PUBLISHED = 'published';
    case FAILED = 'failed';
}
