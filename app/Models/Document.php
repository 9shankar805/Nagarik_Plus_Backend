<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\EncryptionService;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    const TYPES = [
        'national_id'         => 'National ID',
        'passport'            => 'Passport',
        'driving_license'     => 'Driving License',
        'pan'                  => 'PAN Card',
        'citizenship'          => 'Citizenship',
        'voter_id'             => 'Voter ID',
        'birth_certificate'   => 'Birth Certificate',
        'marriage_certificate' => 'Marriage Certificate',
        'migration_certificate'=> 'Migration Certificate',
        'death_certificate'    => 'Death Certificate',
        'vehicle_bluebook'    => 'Vehicle Bluebook',
        'insurance'            => 'Insurance',
        'medical'              => 'Medical',
        'property'             => 'Property',
        'academic'             => 'Academic',
        'nea_bill'             => 'NEA Bill',
        'gunaso'               => 'Gunaso / Grievance',
        'press_pass'           => 'Press Pass',
        'other'                => 'Other',
    ];

    protected $fillable = [
        'user_id', 'title', 'type', 'document_number',
        'encrypted_data', 'file_path', 'file_name', 'mime_type', 'file_size',
        'issue_date', 'expiry_date', 'issued_by', 'status',
        'is_verified', 'reminder_enabled', 'reminder_days_before', 'metadata',
    ];

    protected $hidden = [
        'encrypted_data',
        'file_path',
    ];

    protected $casts = [
        'issue_date'        => 'date',
        'expiry_date'       => 'date',
        'metadata'          => 'array',
        'is_verified'       => 'boolean',
        'reminder_enabled'  => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reminder()
    {
        return $this->hasOne(Reminder::class);
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function daysUntilExpiry(): ?int
    {
        if (!$this->expiry_date) return null;
        return now()->diffInDays($this->expiry_date, false);
    }

    public function scopeExpiringSoon($query, int $days = 90)
    {
        return $query->whereNotNull('expiry_date')
                     ->where('expiry_date', '<=', now()->addDays($days))
                     ->where('expiry_date', '>=', now());
    }
}
