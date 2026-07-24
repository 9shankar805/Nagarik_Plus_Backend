<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'pin_code',
        'biometric_enabled',
        'device_id',
        'last_active_at',
        'role',
        'avatar',
        'profile_photo',
        'is_active',
        'cloud_sync_enabled',
        'last_synced_at',
        'preferred_language',
        'theme',
        'notification_preferences',
        'dob',
        'address',
        'citizenship_number',
        'banned_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'pin_code',
    ];

    protected $casts = [
        'email_verified_at'          => 'datetime',
        'last_active_at'             => 'datetime',
        'biometric_enabled'          => 'boolean',
        'is_active'                  => 'boolean',
        'cloud_sync_enabled'         => 'boolean',
        'last_synced_at'             => 'datetime',
        'banned_at'                  => 'datetime',
        'notification_preferences'   => 'array',
        'password'                   => 'hashed',
    ];

    /** Returns true if the user has been banned. */
    public function isBanned(): bool
    {
        return !is_null($this->banned_at);
    }

    /**
     * Returns true if the given notification category is enabled.
     * Defaults to true if the preference has not been set.
     */
    public function hasNotificationEnabled(string $category): bool
    {
        $prefs = $this->notification_preferences ?? [];
        return (bool) ($prefs[$category] ?? true);
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function testAttempts()
    {
        return $this->hasMany(TestAttempt::class);
    }

    public function aiConversations()
    {
        return $this->hasMany(AiConversation::class);
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

    // News social relations
    public function newsLikes()
    {
        return $this->hasMany(NewsLike::class);
    }

    public function newsBookmarks()
    {
        return $this->hasMany(NewsBookmark::class);
    }

    public function newsComments()
    {
        return $this->hasMany(NewsComment::class);
    }

    public function newsShares()
    {
        return $this->hasMany(NewsShare::class);
    }

    // Advisors relations
    public function advisor()
    {
        return $this->hasOne(Advisor::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function advisorReviews()
    {
        return $this->hasMany(AdvisorReview::class);
    }

    /**
     * Accessor: decoded notification preferences with defaults applied.
     */
    public function getNotificationPreferencesDefaultsAttribute(): array
    {
        $defaults = [
            'document_reminders'   => true,
            'news_updates'         => true,
            'system_announcements' => true,
            'learning_updates'     => true,
        ];

        return array_merge($defaults, $this->notification_preferences ?? []);
    }
}
