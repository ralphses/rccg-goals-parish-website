<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\enums\MediaCategory;
use App\enums\MediaUploadStatus;
use App\enums\MediaType;
use App\enums\YouTubePublishStatus;
use App\enums\YouTubeVideoFormat;
use App\Support\MediaUrl;
use Illuminate\Support\Str;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'file_name',
        'file_path',
        'thumbnail_path',
        'youtube_source_path',
        'upload_status',
        'upload_last_error',
        'upload_queued_at',
        'upload_completed_at',
        'size',
        'category',
        'uploaded_by',
        'is_public',
        'media_type',
        'publish_to_youtube',
        'youtube_format',
        'youtube_status',
        'youtube_title',
        'youtube_description',
        'youtube_video_id',
        'youtube_video_url',
        'youtube_last_error',
        'youtube_publish_requested_at',
        'youtube_published_at',
    ];

    protected $casts = [
        'category' => MediaCategory::class,
        'is_public' => 'boolean',
        'media_type' => MediaType::class,
        'upload_status' => MediaUploadStatus::class,
        'upload_queued_at' => 'datetime',
        'upload_completed_at' => 'datetime',
        'publish_to_youtube' => 'boolean',
        'youtube_format' => YouTubeVideoFormat::class,
        'youtube_status' => YouTubePublishStatus::class,
        'youtube_publish_requested_at' => 'datetime',
        'youtube_published_at' => 'datetime',
    ];

    public function mediable()
    {
        return $this->morphTo();
    }

    public function getVisualPathAttribute(): string
    {
        if ($this->media_type === MediaType::VIDEO && !empty($this->thumbnail_path)) {
            return $this->thumbnail_path;
        }

        return $this->file_path;
    }

    public function getVisualUrlAttribute(): string
    {
        return MediaUrl::toPublicUrl($this->visual_path) ?? '';
    }

    public function getFileUrlAttribute(): string
    {
        return MediaUrl::toPublicUrl($this->file_path) ?? '';
    }

    public function getPublicVideoUrlAttribute(): string
    {
        if ($this->media_type !== MediaType::VIDEO) {
            return $this->file_url;
        }

        return $this->youtube_embed_url ?: $this->file_url;
    }

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if (blank($this->youtube_video_url)) {
            return null;
        }

        $parts = parse_url($this->youtube_video_url);
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

        return 'https://www.youtube.com/watch?v=' . $videoId;
    }

    public function canRetryUploadProcessing(): bool
    {
        return $this->media_type === MediaType::VIDEO
            && $this->upload_status === MediaUploadStatus::FAILED
            && filled($this->youtube_source_path);
    }

    public function canRetryYouTubePublish(): bool
    {
        return $this->media_type === MediaType::VIDEO
            && $this->publish_to_youtube
            && $this->upload_status === MediaUploadStatus::READY
            && $this->youtube_status === YouTubePublishStatus::FAILED;
    }
}
