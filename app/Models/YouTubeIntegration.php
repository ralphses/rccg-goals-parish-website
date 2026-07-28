<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YouTubeIntegration extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id',
        'channel_title',
        'channel_thumbnail_url',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'last_used_at',
        'last_error',
        'connected_by',
    ];

    protected function casts(): array
    {
        return [
            'access_token' => 'encrypted',
            'refresh_token' => 'encrypted',
            'token_expires_at' => 'datetime',
            'last_used_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'connected_by');
    }

    public function hasValidToken(): bool
    {
        return filled($this->access_token) && filled($this->refresh_token);
    }
}
