<?php

namespace App\Models;

use App\enums\MediaType;
use App\enums\TestimonyAnnouncementType;
use App\Support\HumanAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimony extends Model
{
    use HasFactory;

    protected $fillable = [
        'testifier_name',
        'testifier_phone',
        'testifier_email',
        'title',
        'content',

        'announce_in_service',
        'announcement_type',

        'is_featured',
        'is_approved',
        'approved_at',
        'announced',
        'announced_at'
    ];

    protected $casts = [
        'announce_in_service' => 'boolean',
        'announced' => 'boolean',
        'announcement_type' => TestimonyAnnouncementType::class
    ];

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function getImageUrlAttribute(): string
    {
        $image = $this->relationLoaded('media')
            ? $this->media->first(fn (Media $media) => $media->media_type === MediaType::IMAGE)
            : $this->media()->where('media_type', MediaType::IMAGE->value)->oldest('id')->first();

        return $image?->visual_url ?: HumanAvatar::url();
    }
}
