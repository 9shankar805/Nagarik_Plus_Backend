<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'ip_address',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // -------------------------------------------------------------------------
    // Static helpers
    // -------------------------------------------------------------------------

    /**
     * Create and persist a new activity log entry.
     *
     * @param  string        $action       e.g. 'document_added', 'user_banned', 'login'
     * @param  User|null     $user         The acting user, or null for system actions
     * @param  string        $description  Human-readable description of the event
     * @param  array         $extra        Optional keys: 'subject_type', 'subject_id'
     */
    public static function record(
        string $action,
        ?\App\Models\User $user,
        string $description,
        array $extra = []
    ): self {
        return static::create([
            'user_id'      => $user?->id,
            'action'       => $action,
            'description'  => $description,
            'ip_address'   => request()->ip(),
            'subject_type' => $extra['subject_type'] ?? null,
            'subject_id'   => $extra['subject_id'] ?? null,
        ]);
    }
}
