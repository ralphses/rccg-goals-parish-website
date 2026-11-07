<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BirthdayBoardComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'celebration_date',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'celebration_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
