<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\MediaUrl;

class SermonAttachment extends Model
{
    /** @use HasFactory<\Database\Factories\SermonAttachmentFactory> */
    use HasFactory;

    protected $fillable = [
        'sermon_id',
        'file_path',
        'file_name',
        'file_type',
    ];

    public function getFileUrlAttribute(): ?string
    {
        return MediaUrl::toPublicUrl($this->file_path);
    }
}
