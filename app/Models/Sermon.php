<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'audio_url',
        'video_url',
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
}