<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrievanceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_np',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
