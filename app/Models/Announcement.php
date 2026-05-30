<?php

namespace App\Models;

use App\Enums\AnnouncementFrequency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'service_date',
        'frequency',
        'is_active',
        'is_approved',
        'last_announced_at'
    ];

    protected $casts = [
        'service_date' => 'date',
        'last_announced_at' => 'datetime',
        'frequency' => AnnouncementFrequency::class,
        'is_active' => 'boolean',
        'is_approved' => 'boolean'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // polymorphic media
    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}