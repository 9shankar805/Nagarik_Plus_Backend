<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvisorReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'advisor_id', 'consultation_id', 'rating', 'comment', 'comment_np'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function advisor()
    {
        return $this->belongsTo(Advisor::class);
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }
}
