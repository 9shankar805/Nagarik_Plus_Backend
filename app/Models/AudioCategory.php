<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AudioCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'sort_order',
    ];

    public function audio()
    {
        return $this->hasMany(Audio::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
