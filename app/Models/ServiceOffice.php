<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOffice extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id', 'name', 'address', 'district', 'province',
        'latitude', 'longitude', 'phone', 'email', 'office_hours', 'is_active',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
        'is_active' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(CitizenService::class, 'service_id');
    }
}
