<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoadSign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'name_np', 'meaning', 'meaning_np',
        'category', 'image_url', 'color_code', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];
}
