<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'leader_id',
        'status',
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
}