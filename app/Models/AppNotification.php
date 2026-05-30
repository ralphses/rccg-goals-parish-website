<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    /** @use HasFactory<\Database\Factories\AppNotificationFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'is_read',
        'link',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}