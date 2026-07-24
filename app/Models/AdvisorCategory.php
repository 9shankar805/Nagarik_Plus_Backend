<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvisorCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'name_np', 'description', 'description_np',
        'icon', 'color', 'sort_order', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function advisors()
    {
        return $this->hasMany(Advisor::class, 'category_id');
    }
}
