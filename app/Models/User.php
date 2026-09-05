<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['name', 'email', 'kyc_status', 'role'])->logOnlyDirty();
    }

    protected $fillable = [
        'name',
        'email',
        'google_id',
        'apple_id',
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
        return in_array($this->role, ['admin', 'super_admin', 'learning_admin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isLearningAdmin(): bool
    {
        return $this->role === 'learning_admin';
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

    // Calling relations
    public function callsAsCaller()
    {
        return $this->hasMany(Call::class, 'caller_id');
    }

    public function callsAsReceiver()
    {
        return $this->hasMany(Call::class, 'receiver_id');
    }

    public function allCalls()
    {
        return Call::where('caller_id', $this->id)
            ->orWhere('receiver_id', $this->id);
    }

    // Learning Center — Ambition Guru features
    public function streak()
    {
        return $this->hasOne(UserStreak::class);
    }

    public function studyActivity()
    {
        return $this->hasMany(UserStudyActivity::class);
    }

    public function dailyQuizEntries()
    {
        return $this->hasMany(DailyQuizEntry::class);
    }

    public function achievements()
    {
        return $this->hasMany(UserAchievement::class);
    }

    public function chapterRatings()
    {
        return $this->hasMany(ChapterRating::class);
    }

    public function flashcardProgress()
    {
        return $this->hasMany(UserFlashcardProgress::class);
    }

    public function practiceSessions()
    {
        return $this->hasMany(PracticeSession::class);
    }

    // Learning Center relations
    public function testSessions()
    {
        return $this->hasMany(TestSession::class);
    }

    public function competitionRegistrations()
    {
        return $this->hasMany(CompetitionRegistration::class);
    }

    public function competitionAttempts()
    {
        return $this->hasMany(CompetitionAttempt::class);
    }

    public function learningProgress()
    {
        return $this->hasMany(UserLearningProgress::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(UserBookmark::class);
    }

    public function leaderboardEntries()
    {
        return $this->hasMany(LeaderboardEntry::class);
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
