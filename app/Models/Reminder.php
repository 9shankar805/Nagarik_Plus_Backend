<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'document_id', 'title', 'description',
        'due_date', 'days_before', 'is_enabled', 'last_notified_at',
    ];

    protected $casts = [
        'due_date'         => 'date',
        'is_enabled'       => 'boolean',
        'last_notified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function daysRemaining(): int
    {
        return now()->diffInDays($this->due_date, false);
    }

    public function urgencyLevel(): string
    {
        $days = $this->daysRemaining();
        if ($days < 0)  return 'expired';
        if ($days < 30) return 'urgent';
        if ($days < 90) return 'warning';
        return 'normal';
    }

    public function scopeDue($query)
    {
        return $query->where('is_enabled', true)
                     ->whereDate('due_date', '>', now());
    }
}
