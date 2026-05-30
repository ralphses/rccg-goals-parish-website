<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\MediaCategory;
use App\Enums\MediaType;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'file_name',
        'file_path',
        'size',
        'category',
        'uploaded_by',
        'is_public',
        'media_type'
    ];

    protected $casts = [
        'category' => MediaCategory::class,
        'is_public' => 'boolean',
        'media_type' => MediaType::class
    ];

    public function mediable()
    {
        return $this->morphTo();
    }
}