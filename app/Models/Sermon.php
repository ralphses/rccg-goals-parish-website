<?php

namespace App\Models;

use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Support\MediaUrl;
use Illuminate\Support\Str;

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
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
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

    public function getPublicVideoUrlAttribute(): ?string
    {
        return $this->youtube_embed_url ?: $this->video_url;
    }

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if (blank($this->video_url)) {
            return null;
        }

        $parts = parse_url($this->video_url);
        $host = Str::lower($parts['host'] ?? '');
        $path = trim((string) ($parts['path'] ?? ''), '/');
        $videoId = null;

        if (str_contains($host, 'youtu.be')) {
            $videoId = $path;
        } elseif (str_contains($host, 'youtube.com')) {
            if ($path === 'watch') {
                parse_str($parts['query'] ?? '', $query);
                $videoId = $query['v'] ?? null;
            } elseif (str_starts_with($path, 'embed/')) {
                $videoId = Str::after($path, 'embed/');
            } elseif (str_starts_with($path, 'shorts/')) {
                $videoId = Str::after($path, 'shorts/');
            }
        }

        if (blank($videoId)) {
            return null;
        }

        return 'https://www.youtube.com/embed/' . $videoId;
    }
}
