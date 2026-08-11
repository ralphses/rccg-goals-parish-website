<?php

namespace App\Models;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\MediaUrl;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'event_date',
        'location',
        'department_id',
        'status',
        'image',
        'image_media_id',
        'video_link',
        'video_media_id',
        'description_heading',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function imageMedia()
    {
        return $this->belongsTo(Media::class, 'image_media_id');
    }

    public function videoMedia()
    {
        return $this->belongsTo(Media::class, 'video_media_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return MediaUrl::toPublicUrl($this->image);
    }
}
