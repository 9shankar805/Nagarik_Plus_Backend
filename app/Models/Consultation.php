<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'advisor_id', 'status', 'type', 'scheduled_at',
        'started_at', 'ended_at', 'notes', 'amount', 'payment_status', 'payment_method'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function advisor()
    {
        return $this->belongsTo(Advisor::class);
    }

    public function review()
    {
        return $this->hasOne(AdvisorReview::class);
    }
}
