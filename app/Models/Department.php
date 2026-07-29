<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\MediaUrl;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'leader_id',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

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

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return MediaUrl::toPublicUrl($this->image);
    }
}
