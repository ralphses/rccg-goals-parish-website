<?php

namespace App\Models;

use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Support\MediaUrl;

class Sermon extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'message',
        'sermon_date',
        'duration',
        'speaker_id',
        'cover_image',
        'cover_media_id',
        'audio_url',
        'audio_media_id',
        'video_url',
        'video_media_id',
        'status',
        'published_at'
    ];

    protected $casts = [
        'sermon_date' => 'date',
        'published_at' => 'datetime'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function speaker()
    {
        return $this->belongsTo(User::class, 'speaker_id');
    }

    public function attachments()
    {
        return $this->hasMany(SermonAttachment::class);
    }

    public function coverMedia()
    {
        return $this->belongsTo(Media::class, 'cover_media_id');
    }

    public function audioMedia()
    {
        return $this->belongsTo(Media::class, 'audio_media_id');
    }

    public function videoMedia()
    {
        return $this->belongsTo(Media::class, 'video_media_id');
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return MediaUrl::toPublicUrl($this->cover_image);
    }
}
