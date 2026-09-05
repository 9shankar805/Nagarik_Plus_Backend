<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'description',
        'preview_image',
        'template_file_path',
        'template_file_name',
        'template_file_size',
        'placeholder_fields',
        'sort_order',
        'is_active',
        'is_featured',
        'download_count',
        'guidelines',
    ];

    protected $casts = [
        'placeholder_fields' => 'array',
        'is_active'          => 'boolean',
        'is_featured'        => 'boolean',
        'download_count'     => 'integer',
        'sort_order'         => 'integer',
    ];

    public const CATEGORIES = [
        'citizenship'         => 'Citizenship',
        'national_id'         => 'National ID',
        'passport'            => 'Passport',
        'driving_license'     => 'Driving License',
        'pan'                 => 'PAN Card',
        'voter_id'            => 'Voter ID',
        'birth_certificate'   => 'Birth Certificate',
        'marriage_certificate' => 'Marriage Certificate',
        'migration_certificate'=> 'Migration Certificate',
        'death_certificate'    => 'Death Certificate',
        'vehicle_bluebook'    => 'Vehicle Bluebook',
        'vehicle_license_plate' => 'License Plate Registration',
        'insurance'           => 'Insurance',
        'medical'             => 'Medical / Health',
        'property'            => 'Property / Land',
        'academic'            => 'Academic / School / College',
        'business'            => 'Business / Company Registration',
        'tax'                 => 'Tax Forms',
        'nea_bill'            => 'NEA Bill',
        'gunaso'              => 'Gunaso / Grievance',
        'press_pass'          => 'Press Pass',
        'other'               => 'Other',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('title', 'asc');
    }

    public function incrementDownload(): self
    {
        $this->increment('download_count');
        return $this;
    }
}
