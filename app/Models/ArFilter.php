<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArFilter extends Model
{
    protected $fillable = [
        'title',
        'engine',
        'filter_file_url',
        'thumbnail_url',
        'is_active',
    ];
}
