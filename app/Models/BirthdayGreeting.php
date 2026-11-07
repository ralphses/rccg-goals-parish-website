<?php

namespace App\Models;

use App\enums\UserStatus;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BirthdayGreeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'celebration_date',
        'message',
        'media_id',
        'generated_by',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'celebration_date' => 'date',
            'published_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null;
    }

    /**
     * Limit a query to cards that are safe to display publicly on a given Lagos date.
     */
    public function scopePublicForDate(Builder $query, CarbonInterface $date): Builder
    {
        return $query
            ->whereDate('celebration_date', $date->toDateString())
            ->whereNotNull('published_at')
            ->whereHas('media', fn (Builder $media) => $media->where('is_public', true))
            ->whereHas('user', function (Builder $user) use ($date) {
                $user->where('status', UserStatus::ACTIVE->value)
                    ->whereMonth('birthday', $date->month)
                    ->whereDay('birthday', $date->day);
            });
    }
}
