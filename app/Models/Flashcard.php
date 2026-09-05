<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flashcard extends Model
{
    use HasFactory;

    protected $fillable = [
        'flashcard_set_id',
        'front_en', 'front_np',
        'back_en', 'back_np',
        'image_url', 'display_order', 'is_active',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'display_order' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function set()
    {
        return $this->belongsTo(FlashcardSet::class, 'flashcard_set_id');
    }

    public function userProgress()
    {
        return $this->hasMany(UserFlashcardProgress::class);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function getProgressForUser(int $userId): ?UserFlashcardProgress
    {
        return $this->userProgress()->where('user_id', $userId)->first();
    }
}
