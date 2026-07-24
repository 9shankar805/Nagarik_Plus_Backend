<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'name_np', 'category', 'address', 'district', 'province',
        'latitude', 'longitude', 'phone', 'email', 'website',
        'office_hours', 'is_active',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNearby($query, float $lat, float $lng, float $radiusKm = 20)
    {
        // Haversine formula via raw SQL
        return $query->selectRaw(
            '*, ( 6371 * acos( cos( radians(?) ) * cos( radians(latitude) )
            * cos( radians(longitude) - radians(?) )
            + sin( radians(?) ) * sin( radians(latitude) ) ) ) AS distance',
            [$lat, $lng, $lat]
        )->having('distance', '<=', $radiusKm)
         ->orderBy('distance');
    }
}
