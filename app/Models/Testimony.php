<?php

namespace App\Models;

use App\Enums\TestimonyAnnouncementType;
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
}