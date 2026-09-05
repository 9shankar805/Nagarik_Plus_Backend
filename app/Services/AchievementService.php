<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\CompetitionAttempt;
use App\Models\DailyQuizEntry;
use App\Models\LearningChapter;
use App\Models\TestAttempt;
use App\Models\User;
use App\Models\UserAchievement;
use App\Models\UserLearningProgress;
use App\Models\UserStreak;

class AchievementService
{
    /**
     * Check and award all relevant achievements for a user.
     * Returns array of newly awarded achievement slugs.
     */
    public function checkAll(User $user): array
    {
        $streak  = UserStreak::forUser($user->id);
        $awarded = [];

        foreach (Achievement::active()->get() as $achievement) {
            // Skip if already earned
            if (UserAchievement::where('user_id', $user->id)
                    ->where('achievement_id', $achievement->id)->exists()) {
                continue;
            }

            if ($this->qualifies($user, $achievement, $streak)) {
                UserAchievement::create([
                    'user_id'        => $user->id,
                    'achievement_id' => $achievement->id,
                    'earned_at'      => now(),
                ]);
                $awarded[] = $achievement->slug;
            }
        }

        return $awarded;
    }

    /**
     * Quick check after a specific event type to avoid full scan.
     */
    public function checkForType(User $user, string $type): array
    {
        $streak  = UserStreak::forUser($user->id);
        $awarded = [];

        foreach (Achievement::active()->byType($type)->get() as $achievement) {
            if (UserAchievement::where('user_id', $user->id)
                    ->where('achievement_id', $achievement->id)->exists()) {
                continue;
            }

            if ($this->qualifies($user, $achievement, $streak)) {
                UserAchievement::create([
                    'user_id'        => $user->id,
                    'achievement_id' => $achievement->id,
                    'earned_at'      => now(),
                ]);
                $awarded[] = $achievement->slug;
            }
        }

        return $awarded;
    }

    // ── Private qualification logic ────────────────────────────────────────

    private function qualifies(User $user, Achievement $achievement, UserStreak $streak): bool
    {
        $threshold = $achievement->threshold;

        return match ($achievement->type) {
            Achievement::TYPE_STREAK => $streak->current_streak >= $threshold,

            Achievement::TYPE_DAILY =>
                DailyQuizEntry::where('user_id', $user->id)->count() >= $threshold,

            Achievement::TYPE_QUIZ =>
                TestAttempt::where('user_id', $user->id)->count() >= $threshold,

            Achievement::TYPE_TEST =>
                TestAttempt::where('user_id', $user->id)
                    ->where('passed', true)->count() >= $threshold,

            Achievement::TYPE_CHAPTER =>
                UserLearningProgress::where('user_id', $user->id)->count() >= $threshold,

            Achievement::TYPE_COMPETITION =>
                CompetitionAttempt::where('user_id', $user->id)->count() >= $threshold,

            default => false,
        };
    }
}
