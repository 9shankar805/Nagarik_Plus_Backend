<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\ChapterRating;
use App\Models\DailyQuiz;
use App\Models\DailyQuizEntry;
use App\Models\Flashcard;
use App\Models\FlashcardSet;
use App\Models\LearningCategory;
use App\Models\LearningChapter;
use App\Models\PracticeSession;
use App\Models\QuizQuestion;
use App\Models\TestAttempt;
use App\Models\UserAchievement;
use App\Models\UserBookmark;
use App\Models\UserFlashcardProgress;
use App\Models\UserStreak;
use App\Models\UserStudyActivity;
use App\Services\AchievementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LearningFeatureController extends Controller
{

    // =========================================================================
    // DAILY QUIZ
    // =========================================================================

    /** GET /api/v1/learning/daily-quiz  — public, enriched for auth users */
    public function dailyQuiz(Request $request): JsonResponse
    {
        $today = today()->toDateString();
        $catSlug = $request->category_slug;

        $query = DailyQuiz::forToday()->with(['question', 'category:id,slug,name_en,name_np']);
        if ($catSlug) {
            $cat = LearningCategory::where('slug', $catSlug)->first();
            $query->where('learning_category_id', $cat?->id);
        }

        $daily = $query->first();

        if (!$daily) {
            return response()->json(['success' => false, 'message' => 'No daily quiz available today.'], 404);
        }

        $q = $daily->question;
        $userId = $request->user()?->id;
        $entry  = $userId ? $daily->getUserEntry($userId) : null;

        $data = [
            'daily_quiz_id'     => $daily->id,
            'quiz_date'         => $today,
            'category'          => $daily->category ? ['slug' => $daily->category->slug, 'name_en' => $daily->category->name_en] : null,
            'already_answered'  => (bool) $entry,
            'participant_count' => $daily->participant_count,
            'question'          => [
                'id'          => $q->id,
                'question'    => $q->question,
                'question_np' => $q->question_np,
                'options'     => $q->options,
                'options_np'  => $q->options_np,
                'difficulty'  => $q->difficulty,
                'image_url'   => $q->image_url,
            ],
        ];

        // If already answered, reveal correct answer
        if ($entry) {
            $data['your_answer']    = $entry->selected_index;
            $data['is_correct']     = $entry->is_correct;
            $data['correct_index']  = $q->correct_index;
            $data['explanation']    = $q->explanation;
            $data['explanation_np'] = $q->explanation_np;
            $data['correct_count']  = $daily->correct_count;
        }

        return response()->json(['success' => true, 'data' => $data]);
    }

    /** POST /api/v1/learning/daily-quiz/answer  [auth] */
    public function answerDailyQuiz(Request $request): JsonResponse
    {
        $data = $request->validate([
            'daily_quiz_id'  => 'required|integer|exists:daily_quizzes,id',
            'selected_index' => 'required|integer|min:-1|max:3',
        ]);

        $daily = DailyQuiz::where('is_active', true)->findOrFail($data['daily_quiz_id']);

        if ($daily->hasUserAnswered($request->user()->id)) {
            return response()->json(['success' => false, 'message' => 'Already answered today\'s quiz.'], 422);
        }

        $q         = $daily->question;
        $isCorrect = $data['selected_index'] !== -1 && $data['selected_index'] === $q->correct_index;

        $entry = DailyQuizEntry::create([
            'user_id'        => $request->user()->id,
            'daily_quiz_id'  => $daily->id,
            'selected_index' => $data['selected_index'],
            'is_correct'     => $isCorrect,
            'answered_at'    => now(),
        ]);

        // Update streak
        $streak = UserStreak::forUser($request->user()->id);
        $streak->recordActivity();
        $streak->recordDailyQuiz();

        // Check achievements
        $service = app(AchievementService::class);
        $newBadges = array_merge(
            $service->checkForType($request->user(), 'daily'),
            $service->checkForType($request->user(), 'streak')
        );

        return response()->json(['success' => true, 'data' => [
            'is_correct'      => $isCorrect,
            'correct_index'   => $q->correct_index,
            'explanation'     => $q->explanation,
            'explanation_np'  => $q->explanation_np,
            'daily_quiz_streak' => $streak->daily_quiz_streak,
            'study_streak'    => $streak->current_streak,
            'new_badges'      => $newBadges,
        ]]);
    }

    /** GET /api/v1/learning/daily-quiz/history  [auth] */
    public function dailyQuizHistory(Request $request): JsonResponse
    {
        $entries = DailyQuizEntry::where('user_id', $request->user()->id)
            ->with(['dailyQuiz.question:id,question,question_np,correct_index,explanation,explanation_np',
                    'dailyQuiz.category:id,slug,name_en'])
            ->latest('answered_at')
            ->limit(30)
            ->get()
            ->map(fn($e) => [
                'date'           => $e->dailyQuiz->quiz_date?->toDateString(),
                'category'       => $e->dailyQuiz->category?->name_en,
                'question'       => $e->dailyQuiz->question?->question,
                'selected_index' => $e->selected_index,
                'correct_index'  => $e->dailyQuiz->question?->correct_index,
                'is_correct'     => $e->is_correct,
            ]);

        $totalDays    = DailyQuizEntry::where('user_id', $request->user()->id)->count();
        $correctDays  = DailyQuizEntry::where('user_id', $request->user()->id)->where('is_correct', true)->count();

        return response()->json(['success' => true, 'data' => [
            'total_days'     => $totalDays,
            'correct_days'   => $correctDays,
            'accuracy_pct'   => $totalDays > 0 ? round(($correctDays / $totalDays) * 100) : 0,
            'entries'        => $entries,
        ]]);
    }

    // =========================================================================
    // STREAK & ACTIVITY
    // =========================================================================

    /** GET /api/v1/learning/streak  [auth] */
    public function streak(Request $request): JsonResponse
    {
        $streak   = UserStreak::forUser($request->user()->id);
        $calendar = UserStudyActivity::where('user_id', $request->user()->id)
            ->where('activity_date', '>=', now()->subDays(30))
            ->pluck('activity_date')
            ->map(fn($d) => $d->toDateString())
            ->values();

        return response()->json(['success' => true, 'data' => [
            'current_streak'      => $streak->current_streak,
            'longest_streak'      => $streak->longest_streak,
            'total_study_days'    => $streak->total_study_days,
            'daily_quiz_streak'   => $streak->daily_quiz_streak,
            'last_activity_date'  => $streak->last_activity_date?->toDateString(),
            'active_dates_30d'    => $calendar,
        ]]);
    }

    // =========================================================================
    // PRACTICE MODE  (unlimited, un-timed drilling)
    // =========================================================================

    /** POST /api/v1/learning/practice/start  [auth] */
    public function startPractice(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category'           => 'required|string',
            'mode'               => 'required|in:all,wrong_only,bookmarked,difficulty',
            'difficulty'         => 'nullable|in:easy,medium,hard',
            'chapter_id'         => 'nullable|integer|exists:learning_chapters,id',
            'limit'              => 'nullable|integer|min:5|max:100',
        ]);

        $userId = $request->user()->id;
        $limit  = $data['limit'] ?? 20;

        $query = QuizQuestion::active()->byCategory($data['category']);

        if ($data['chapter_id'] ?? null) {
            $query->where('learning_chapter_id', $data['chapter_id']);
        }

        switch ($data['mode']) {
            case PracticeSession::MODE_WRONG_ONLY:
                // Get question IDs the user got wrong in last 50 attempts
                $wrongIds = TestAttempt::where('user_id', $userId)
                    ->where('category', $data['category'])
                    ->latest()
                    ->limit(50)
                    ->get()
                    ->flatMap(fn($a) => collect($a->answers ?? [])
                        ->filter(fn($ans) => ($ans['selected'] ?? -1) !== -1)
                        ->map(function ($ans) {
                            // We need to check correctness — we only stored selected, not is_correct
                            // So fetch and compare
                            $q = QuizQuestion::withoutGlobalScopes()->find($ans['question_id']);
                            return ($q && $ans['selected'] !== $q->correct_index)
                                ? $ans['question_id'] : null;
                        })
                        ->filter()
                    )->unique()->values()->toArray();

                if (empty($wrongIds)) {
                    return response()->json(['success' => false, 'message' => 'No wrong questions found. Great job!'], 404);
                }
                $query->whereIn('id', $wrongIds);
                break;

            case PracticeSession::MODE_BOOKMARKED:
                $bookmarkedIds = UserBookmark::where('user_id', $userId)
                    ->where('bookmarkable_type', QuizQuestion::class)
                    ->pluck('bookmarkable_id');
                if ($bookmarkedIds->isEmpty()) {
                    return response()->json(['success' => false, 'message' => 'No bookmarked questions found.'], 404);
                }
                $query->whereIn('id', $bookmarkedIds);
                break;

            case PracticeSession::MODE_DIFFICULTY:
                if (!empty($data['difficulty'])) {
                    $query->where('difficulty', $data['difficulty']);
                }
                break;
        }

        $questions = $query->inRandomOrder()->limit($limit)->get()
            ->map(fn($q) => [
                'id'          => $q->id,
                'question'    => $q->question,
                'question_np' => $q->question_np,
                'options'     => $q->options,
                'options_np'  => $q->options_np,
                'difficulty'  => $q->difficulty,
                'image_url'   => $q->image_url,
            ]);

        if ($questions->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No questions found for the selected filters.'], 404);
        }

        return response()->json(['success' => true, 'data' => [
            'mode'       => $data['mode'],
            'category'   => $data['category'],
            'difficulty' => $data['difficulty'] ?? null,
            'total'      => $questions->count(),
            'questions'  => $questions,
        ]]);
    }

    /** POST /api/v1/learning/practice/submit  [auth] */
    public function submitPractice(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category'                   => 'required|string',
            'mode'                       => 'required|string',
            'chapter_id'                 => 'nullable|integer',
            'answers'                    => 'required|array|min:1',
            'answers.*.question_id'      => 'required|integer|exists:quiz_questions,id',
            'answers.*.selected'         => 'required|integer|min:-1|max:3',
            'time_taken_seconds'         => 'nullable|integer|min:0',
        ]);

        $questionIds = array_column($data['answers'], 'question_id');
        $questions   = QuizQuestion::withoutGlobalScopes()->whereIn('id', $questionIds)->get()->keyBy('id');

        $correct = 0;
        $results = [];
        foreach ($data['answers'] as $answer) {
            $q = $questions[$answer['question_id']] ?? null;
            if (!$q) continue;
            $selected  = (int) $answer['selected'];
            $isCorrect = $selected !== -1 && $selected === $q->correct_index;
            if ($isCorrect) $correct++;
            $results[] = [
                'question_id'    => $q->id,
                'selected'       => $selected,
                'correct_index'  => $q->correct_index,
                'is_correct'     => $isCorrect,
                'explanation'    => $q->explanation,
                'explanation_np' => $q->explanation_np,
            ];
        }

        $total = count($data['answers']);

        $session = PracticeSession::create([
            'user_id'            => $request->user()->id,
            'category'           => $data['category'],
            'learning_chapter_id'=> $data['chapter_id'] ?? null,
            'mode'               => $data['mode'],
            'questions_answered' => $total,
            'correct_answers'    => $correct,
            'answers'            => $data['answers'],
            'time_taken_seconds' => $data['time_taken_seconds'] ?? null,
            'completed_at'       => now(),
        ]);

        // Update streak & activity
        $streak = UserStreak::forUser($request->user()->id);
        $streak->recordActivity();
        UserStudyActivity::firstOrCreate([
            'user_id'       => $request->user()->id,
            'activity_date' => today(),
        ]);
        UserStudyActivity::where('user_id', $request->user()->id)
            ->where('activity_date', today())
            ->increment('questions_answered', $total);

        $newBadges = app(AchievementService::class)->checkForType($request->user(), 'quiz');

        return response()->json(['success' => true, 'data' => [
            'session_id'       => $session->id,
            'total_questions'  => $total,
            'correct_answers'  => $correct,
            'accuracy_pct'     => $total > 0 ? round(($correct / $total) * 100) : 0,
            'study_streak'     => $streak->current_streak,
            'new_badges'       => $newBadges,
            'results'          => $results,
        ]]);
    }

    // =========================================================================
    // FLASHCARDS
    // =========================================================================

    /** GET /api/v1/learning/flashcard-sets  (public) */
    public function flashcardSets(Request $request): JsonResponse
    {
        $query = FlashcardSet::published()
            ->with('category:id,slug,name_en,name_np,icon,color_code');

        if ($request->category_slug) {
            $cat = LearningCategory::where('slug', $request->category_slug)->first();
            if ($cat) $query->where('learning_category_id', $cat?->id);
        }
        if ($request->chapter_id) {
            $query->where('learning_chapter_id', $request->chapter_id);
        }

        $sets = $query->orderBy('display_order')->get()
            ->map(fn($s) => [
                'id'          => $s->id,
                'title_en'    => $s->title_en,
                'title_np'    => $s->title_np,
                'description' => $s->description,
                'card_count'  => $s->card_count,
                'category'    => $s->category ? ['slug' => $s->category->slug, 'name_en' => $s->category->name_en, 'icon' => $s->category->icon] : null,
            ]);

        return response()->json(['success' => true, 'data' => $sets]);
    }

    /** GET /api/v1/learning/flashcard-sets/{id}  — includes cards + user progress */
    public function flashcardSet(Request $request, int $id): JsonResponse
    {
        $set     = FlashcardSet::published()->with('category:id,slug,name_en')->findOrFail($id);
        $userId  = $request->user()?->id;

        $progressMap = [];
        if ($userId) {
            $progressMap = UserFlashcardProgress::where('user_id', $userId)
                ->whereIn('flashcard_id', $set->flashcards->pluck('id'))
                ->get()->keyBy('flashcard_id');
        }

        $cards = $set->flashcards->map(function ($card) use ($progressMap) {
            $prog = $progressMap[$card->id] ?? null;
            return [
                'id'          => $card->id,
                'front_en'    => $card->front_en,
                'front_np'    => $card->front_np,
                'back_en'     => $card->back_en,
                'back_np'     => $card->back_np,
                'image_url'   => $card->image_url,
                'status'      => $prog ? UserFlashcardProgress::statusLabel($prog->status) : 'new',
                'times_seen'  => $prog?->times_seen ?? 0,
                'mastered'    => $prog?->status === UserFlashcardProgress::STATUS_MASTERED,
                'next_review_at' => $prog?->next_review_at?->toIso8601String(),
            ];
        });

        // Summary stats
        $masteredCount  = $userId ? ($progressMap->where('status', UserFlashcardProgress::STATUS_MASTERED)->count()) : 0;

        return response()->json(['success' => true, 'data' => [
            'set'            => ['id' => $set->id, 'title_en' => $set->title_en, 'title_np' => $set->title_np, 'card_count' => $set->card_count],
            'cards'          => $cards,
            'mastered_count' => $masteredCount,
            'progress_pct'   => $set->card_count > 0 ? round(($masteredCount / $set->card_count) * 100) : 0,
        ]]);
    }

    /** POST /api/v1/learning/flashcards/{id}/review  [auth]
     *  Body: { knew: true|false }
     */
    public function reviewFlashcard(Request $request, int $id): JsonResponse
    {
        $data = $request->validate(['knew' => 'required|boolean']);

        $card = Flashcard::findOrFail($id);

        $progress = UserFlashcardProgress::firstOrCreate(
            ['user_id' => $request->user()->id, 'flashcard_id' => $id],
            ['status' => UserFlashcardProgress::STATUS_NEW]
        );
        $progress->review($data['knew']);

        // Track activity
        $streak = UserStreak::forUser($request->user()->id);
        $streak->recordActivity();

        return response()->json(['success' => true, 'data' => [
            'status'         => UserFlashcardProgress::statusLabel($progress->status),
            'times_seen'     => $progress->times_seen,
            'times_correct'  => $progress->times_correct,
            'next_review_at' => $progress->next_review_at?->toIso8601String(),
            'mastered'       => $progress->status === UserFlashcardProgress::STATUS_MASTERED,
        ]]);
    }

    /** GET /api/v1/learning/flashcards/due  [auth] — cards due for review today */
    public function dueFlashcards(Request $request): JsonResponse
    {
        $due = UserFlashcardProgress::where('user_id', $request->user()->id)
            ->where('status', '<', UserFlashcardProgress::STATUS_MASTERED)
            ->where(fn($q) => $q->whereNull('next_review_at')->orWhere('next_review_at', '<=', now()))
            ->with('flashcard.set:id,title_en')
            ->limit(20)
            ->get()
            ->map(fn($p) => [
                'flashcard_id' => $p->flashcard_id,
                'set_title'    => $p->flashcard->set?->title_en,
                'front_en'     => $p->flashcard->front_en,
                'front_np'     => $p->flashcard->front_np,
                'back_en'      => $p->flashcard->back_en,
                'back_np'      => $p->flashcard->back_np,
                'status'       => UserFlashcardProgress::statusLabel($p->status),
                'times_seen'   => $p->times_seen,
            ]);

        return response()->json(['success' => true, 'data' => ['due_count' => $due->count(), 'cards' => $due]]);
    }

    // =========================================================================
    // CHAPTER RATING
    // =========================================================================

    /** POST /api/v1/learning/chapters/{id}/rate  [auth] */
    public function rateChapter(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        LearningChapter::published()->findOrFail($id);

        ChapterRating::updateOrCreate(
            ['user_id' => $request->user()->id, 'learning_chapter_id' => $id],
            ['rating' => $data['rating'], 'comment' => $data['comment'] ?? null]
        );

        $avg = ChapterRating::where('learning_chapter_id', $id)->avg('rating');

        return response()->json(['success' => true, 'data' => [
            'your_rating' => $data['rating'],
            'avg_rating'  => round($avg, 1),
        ]]);
    }

    // =========================================================================
    // ACHIEVEMENTS / BADGES
    // =========================================================================

    /** GET /api/v1/learning/achievements  (public — shows all) */
    public function achievements(Request $request): JsonResponse
    {
        $all     = Achievement::active()->get();
        $userId  = $request->user()?->id;
        $earned  = $userId
            ? UserAchievement::where('user_id', $userId)->pluck('achievement_id')->toArray()
            : [];

        $data = $all->map(fn($a) => [
            'slug'           => $a->slug,
            'title_en'       => $a->title_en,
            'title_np'       => $a->title_np,
            'description_en' => $a->description_en,
            'description_np' => $a->description_np,
            'icon'           => $a->icon,
            'badge_color'    => $a->badge_color,
            'type'           => $a->type,
            'threshold'      => $a->threshold,
            'earned'         => in_array($a->id, $earned),
            'earned_at'      => $userId
                ? UserAchievement::where('user_id', $userId)
                    ->where('achievement_id', $a->id)
                    ->value('earned_at')
                : null,
        ]);

        return response()->json(['success' => true, 'data' => $data]);
    }

    /** GET /api/v1/learning/achievements/my  [auth] */
    public function myAchievements(Request $request): JsonResponse
    {
        $earned = UserAchievement::where('user_id', $request->user()->id)
            ->with('achievement')
            ->orderByDesc('earned_at')
            ->get()
            ->map(fn($ua) => [
                'slug'        => $ua->achievement->slug,
                'title_en'    => $ua->achievement->title_en,
                'title_np'    => $ua->achievement->title_np,
                'icon'        => $ua->achievement->icon,
                'badge_color' => $ua->achievement->badge_color,
                'type'        => $ua->achievement->type,
                'earned_at'   => $ua->earned_at->toDateTimeString(),
            ]);

        return response()->json(['success' => true, 'data' => [
            'total_earned' => $earned->count(),
            'achievements' => $earned,
        ]]);
    }

    // =========================================================================
    // PRACTICE HISTORY
    // =========================================================================

    /** GET /api/v1/learning/practice/history  [auth] */
    public function practiceHistory(Request $request): JsonResponse
    {
        $sessions = PracticeSession::where('user_id', $request->user()->id)
            ->with('chapter:id,title_en,title_np')
            ->orderByDesc('completed_at')
            ->paginate(20);

        $summary = PracticeSession::where('user_id', $request->user()->id)
            ->selectRaw('
                COUNT(*)                        AS total_sessions,
                SUM(questions_answered)         AS total_questions,
                SUM(correct_answers)            AS total_correct,
                ROUND(AVG(
                    CASE WHEN questions_answered > 0
                         THEN correct_answers / questions_answered * 100
                         ELSE 0 END
                ), 1)                           AS avg_accuracy_pct
            ')
            ->first();

        $items = $sessions->map(fn ($s) => [
            'id'                 => $s->id,
            'mode'               => $s->mode,
            'category'           => $s->category,
            'chapter'            => $s->chapter
                ? ['id' => $s->chapter->id, 'title_en' => $s->chapter->title_en]
                : null,
            'questions_answered' => $s->questions_answered,
            'correct_answers'    => $s->correct_answers,
            'accuracy_pct'       => $s->accuracy,          // from getAccuracyAttribute()
            'time_taken_seconds' => $s->time_taken_seconds,
            'completed_at'       => $s->completed_at?->toDateTimeString(),
        ]);

        return response()->json(['success' => true, 'data' => [
            'summary'  => [
                'total_sessions'   => (int) ($summary->total_sessions ?? 0),
                'total_questions'  => (int) ($summary->total_questions ?? 0),
                'total_correct'    => (int) ($summary->total_correct ?? 0),
                'avg_accuracy_pct' => (float) ($summary->avg_accuracy_pct ?? 0),
            ],
            'sessions'      => $items,
            'current_page'  => $sessions->currentPage(),
            'last_page'     => $sessions->lastPage(),
            'total'         => $sessions->total(),
        ]]);
    }
}
