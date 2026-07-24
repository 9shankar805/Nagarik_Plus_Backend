<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialService extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_en',
        'title_np',
        'subtitle_en',
        'subtitle_np',
        'icon',
        'color',
        'image_asset',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
