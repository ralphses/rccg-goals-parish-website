<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stream extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'youtube_url',
        'is_live',
        'scheduled_at',
    ];

    protected $casts = [
        'is_live' => 'boolean',
        'scheduled_at' => 'datetime',
    ];
}