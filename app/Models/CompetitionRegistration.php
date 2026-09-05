<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id', 'user_id',
        'registered_at', 'paid_at',
        'payment_reference', 'payment_method', 'payment_status',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'paid_at'       => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function markAsPaid(string $reference, string $method = 'free'): void
    {
        $this->update([
            'payment_status'    => 'paid',
            'paid_at'           => now(),
            'payment_reference' => $reference,
            'payment_method'    => $method,
        ]);
    }
}
