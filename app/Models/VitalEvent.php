<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VitalEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_en',
        'title_np',
        'bg_color',
        'image_asset',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
