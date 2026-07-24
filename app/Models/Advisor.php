<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advisor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'category_id', 'name', 'name_np', 'email', 'phone',
        'bio', 'bio_np', 'specialization', 'avatar_url', 'rating',
        'total_reviews', 'consultation_fee', 'currency', 'availability',
        'is_online', 'is_available', 'is_verified', 'sort_order'
    ];

    protected $casts = [
        'availability' => 'array',
        'is_online' => 'boolean',
        'is_available' => 'boolean',
        'is_verified' => 'boolean',
        'rating' => 'decimal:2',
        'consultation_fee' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(AdvisorCategory::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function reviews()
    {
        return $this->hasMany(AdvisorReview::class);
    }
}
