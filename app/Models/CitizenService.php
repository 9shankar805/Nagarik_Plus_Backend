<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CitizenService extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug', 'title', 'title_np', 'description', 'description_np',
        'category', 'icon', 'color', 'eligibility',
        'required_documents', 'application_steps', 'fee', 'fee_updated_at',
        'processing_time', 'faqs', 'official_url', 'video_url', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'required_documents' => 'array',
        'application_steps'  => 'array',
        'faqs'               => 'array',
        'fee_updated_at'     => 'date',
        'is_active'          => 'boolean',
    ];

    public function offices()
    {
        return $this->hasMany(ServiceOffice::class, 'service_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
