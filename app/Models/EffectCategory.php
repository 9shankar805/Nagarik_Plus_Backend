<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EffectCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'sort_order',
    ];

    public function effects()
    {
        return $this->hasMany(Effect::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
