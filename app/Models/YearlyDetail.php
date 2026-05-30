<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearlyDetail extends Model
{
    /** @use HasFactory<\Database\Factories\YearlyDetailFactory> */
    use HasFactory;

     protected $fillable = [
        'current_year',
        'year_theme',
        'year_scripture',
        'year_scripture_content',
        'current_month',
        'current_month_theme',
        'current_month_scripture',
        'current_month_scripture_content',
    ];
}