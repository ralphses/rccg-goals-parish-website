<?php

namespace App\Models;

use App\enums\UserRole;
use App\enums\UserStatus;
use App\Support\MediaUrl;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'status',
        'last_login_at',
        'address',
        'day_joined',
        'what_attracted_you',
        'state_of_origin',
        'occupation',
        'hobbies',
        'favourite_quote',
        'birthday',
        'can_login'
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => UserStatus::class,
            'last_login_at' => 'datetime',
            'day_joined' => 'date',
            'birthday' => 'date',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function departments()
    {
        return $this->belongsToMany(Department::class)
            ->withTimestamps();
    }

    public function events()
    {
        return $this->belongsToMany(Event::class)
            ->withTimestamps();
    }

    public function appNotifications()
    {
        return $this->hasMany(AppNotification::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Role Helpers
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isEditor(): bool
    {
        return $this->role === UserRole::EDITOR;
    }

    public function isPastor(): bool
    {
        return $this->role === UserRole::PASTOR;
    }

    public function isMedia(): bool
    {
        return $this->role === UserRole::MEDIA;
    }

    public function isMember(): bool
    {
        return $this->role === UserRole::MEMBER;
    }

    /*
    |--------------------------------------------------------------------------
    | Status Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === UserStatus::ACTIVE;
    }

    public function isSuspended(): bool
    {
        return $this->status === UserStatus::SUSPENDED;
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return MediaUrl::toPublicUrl($this->avatar);
    }
}
