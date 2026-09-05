<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ChapterRating;
use App\Models\DailyQuiz;
use App\Models\DailyQuizEntry;
use App\Models\Flashcard;
use App\Models\LearningCategory;
use App\Models\LearningChapter;
use App\Models\QuizQuestion;
use App\Models\TestAttempt;
use App\Models\UserAchievement;
use App\Models\UserStreak;
use App\Models\UserLearningProgress;
use App\Models\PracticeSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdvancedLearningController extends Controller
{
    // =========================================================================
    // DAILY QUIZ
    // =========================================================================

    /** GET /api/v1/learning/daily-quiz */
    public function dailyQuiz(Request $request): JsonResponse
    {
        // For simplicity, just get a random active question of the day or generate one
        $today = today();
        $daily = DailyQuiz::firstOrCreate(
            ['quiz_date' => $today],
            ['quiz_question_id' => QuizQuestion::inRandomOrder()->first()?->id]
        );

        if (!$daily || !$daily->quiz_question_id) {
            return response()->json(['success' => false, 'message' => 'No daily quiz available.'], 404);
        }

        $question = QuizQuestion::find($daily->quiz_question_id);
        
        $attempted = false;
        if ($request->user()) {
            $attempted = DailyQuizEntry::where('user_id', $request->user()->id)
                ->where('daily_quiz_id', $daily->id)->exists();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $daily->id,
                'date' => $daily->quiz_date->toDateString(),
                'question' => [
                    'id' => $question->id,
                    'question' => $question->question,
                    'options' => $question->options,
                    'difficulty' => $question->difficulty, // New requirement
                ],
                'already_attempted' => $attempted,
            ]
        ]);
    }

    /** POST /api/v1/learning/daily-quiz/submit */
    public function submitDailyQuiz(Request $request): JsonResponse
    {
        $request->validate([
            'daily_quiz_id' => 'required|exists:daily_quizzes,id',
            'selected' => 'required|integer|min:0|max:3',
        ]);

        $daily = DailyQuiz::findOrFail($request->daily_quiz_id);
        $user = $request->user();

        if (DailyQuizEntry::where('user_id', $user->id)->where('daily_quiz_id', $daily->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Already attempted today.'], 422);
        }

        $question = QuizQuestion::find($daily->quiz_question_id);
        $isCorrect = $request->selected === $question->correct_index;

        DailyQuizEntry::create([
            'daily_quiz_id' => $daily->id,
            'user_id' => $user->id,
            'selected' => $request->selected,
            'is_correct' => $isCorrect,
        ]);

        // Update streak
        $streak = UserStreak::forUser($user->id);
        $streak->recordDailyQuiz();
        $streak->recordActivity();

        return response()->json([
            'success' => true,
            'data' => [
                'is_correct' => $isCorrect,
                'correct_index' => $question->correct_index,
                'explanation' => $question->explanation,
                'current_streak' => $streak->daily_quiz_streak,
            ]
        ]);
    }

    // =========================================================================
    // FLASHCARDS
    // =========================================================================

    /** GET /api/v1/learning/flashcards */
    public function flashcards(Request $request): JsonResponse
    {
        $query = Flashcard::where('is_active', true);
        
        if ($request->category_id) {
            $query->where('learning_category_id', $request->category_id);
        }
        if ($request->chapter_id) {
            $query->where('learning_chapter_id', $request->chapter_id);
        }

        $flashcards = $query->inRandomOrder()->limit(50)->get()->map(function($f) {
            return [
                'id' => $f->id,
                'term' => $f->term,
                'definition' => $f->definition,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $flashcards,
        ]);
    }

    // =========================================================================
    // PRACTICE MODE & WRONG-ONLY
    // =========================================================================

    /** GET /api/v1/learning/practice */
    public function practice(Request $request): JsonResponse
    {
        $query = QuizQuestion::active();

        if ($request->category) {
            $query->where('category', $request->category);
        }
        if ($request->chapter_id) {
            $query->where('learning_chapter_id', $request->chapter_id);
        }

        $questions = $query->inRandomOrder()->limit(20)->get()->map(function($q) {
            return [
                'id' => $q->id,
                'question' => $q->question,
                'options' => $q->options,
                'difficulty' => $q->difficulty,
                'explanation' => $q->explanation,
                'correct_index' => $q->correct_index, // In practice mode, we can return the correct answer for immediate feedback
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $questions,
        ]);
    }

    /** GET /api/v1/learning/practice/wrong-only */
    public function wrongOnlyPractice(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        // Fetch all questions the user got wrong in past test attempts
        $attempts = TestAttempt::where('user_id', $userId)->get();
        $wrongQuestionIds = collect();

        foreach ($attempts as $attempt) {
            if (!$attempt->answers) continue;
            foreach ($attempt->answers as $ans) {
                // If it was answered wrong (selected != correct)
                if (isset($ans['selected']) && isset($ans['correct_index'])) {
                    if ($ans['selected'] !== $ans['correct_index'] && $ans['selected'] !== -1) {
                        $wrongQuestionIds->push($ans['question_id']);
                    }
                }
            }
        }

        $uniqueWrongIds = $wrongQuestionIds->unique()->values()->toArray();

        if (empty($uniqueWrongIds)) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $questions = QuizQuestion::active()->whereIn('id', $uniqueWrongIds)->inRandomOrder()->limit(20)->get()->map(function($q) {
            return [
                'id' => $q->id,
                'question' => $q->question,
                'options' => $q->options,
                'difficulty' => $q->difficulty,
                'explanation' => $q->explanation,
                'correct_index' => $q->correct_index,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $questions,
        ]);
    }

    // =========================================================================
    // CHAPTER RATINGS
    // =========================================================================

    /** POST /api/v1/learning/chapters/{id}/rate */
    public function rateChapter(Request $request, $id): JsonResponse
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
        ]);

        $chapter = LearningChapter::findOrFail($id);

        ChapterRating::updateOrCreate(
            ['user_id' => $request->user()->id, 'learning_chapter_id' => $chapter->id],
            ['rating' => $request->rating, 'comment' => $request->review_text] // Schema has 'comment' instead of 'review_text'
        );

        return response()->json([
            'success' => true,
            'message' => 'Rating submitted successfully.'
        ]);
    }

    // =========================================================================
    // PERSONAL DASHBOARD
    // =========================================================================

    /** GET /api/v1/learning/dashboard */
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        $streak = UserStreak::forUser($user->id);

        $totalChapters = LearningChapter::published()->count();
        $readChapters = UserLearningProgress::where('user_id', $user->id)->count();
        $syllabusPercent = $totalChapters > 0 ? round(($readChapters / $totalChapters) * 100) : 0;

        $badges = UserAchievement::where('user_id', $user->id)->get()->map(fn($b) => [
            'name' => $b->achievement_name ?? 'Badge',
            'awarded_at' => $b->created_at->toDateString(),
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'current_streak' => $streak->current_streak,
                'longest_streak' => $streak->longest_streak,
                'daily_quiz_streak' => $streak->daily_quiz_streak,
                'syllabus_completion_percentage' => $syllabusPercent,
                'chapters_read' => $readChapters,
                'total_chapters' => $totalChapters,
                'badges' => $badges,
            ]
        ]);
    }

    // =========================================================================
    // SYLLABUS TRACKER
    // =========================================================================

    /**
     * GET /api/v1/advanced-learning/syllabus/{categorySlug}  [auth]
     * Per-chapter completion map for a category — shows read vs unread.
     */
    public function syllabus(Request $request, string $categorySlug): JsonResponse
    {
        $category = LearningCategory::where('slug', $categorySlug)->firstOrFail();

        $chapters = LearningChapter::published()
            ->where('learning_category_id', $category->id)
            ->orderBy('display_order')
            ->get(['id', 'title_en', 'title_np', 'display_order', 'read_time_minutes']);

        $readIds = UserLearningProgress::where('user_id', $request->user()->id)
            ->whereIn('learning_chapter_id', $chapters->pluck('id'))
            ->pluck('learning_chapter_id')
            ->flip();

        $items = $chapters->map(fn ($ch) => [
            'id'                 => $ch->id,
            'title_en'           => $ch->title_en,
            'title_np'           => $ch->title_np,
            'display_order'      => $ch->display_order,
            'read_time_minutes'  => $ch->read_time_minutes,
            'completed'          => isset($readIds[$ch->id]),
        ]);

        $total    = $chapters->count();
        $completed = $items->where('completed', true)->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'category'       => ['slug' => $category->slug, 'name_en' => $category->name_en],
                'total_chapters' => $total,
                'completed'      => $completed,
                'completion_pct' => $total > 0 ? round(($completed / $total) * 100) : 0,
                'chapters'       => $items,
            ],
        ]);
    }

    // =========================================================================
    // TOPIC MASTERY MAP
    // =========================================================================

    /**
     * GET /api/v1/advanced-learning/mastery  [auth]
     * Aggregated correct/wrong counts grouped by topic_id across all test attempts.
     */
    public function masteryMap(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        // Pull all test attempts for this user that have answers stored
        $attempts = TestAttempt::where('user_id', $userId)
            ->whereNotNull('answers')
            ->get(['answers', 'category']);

        // Aggregate per topic
        $topicStats = [];   // [ topic_id => ['correct'=>n, 'total'=>n, 'category'=>s] ]

        foreach ($attempts as $attempt) {
            $answers = is_array($attempt->answers) ? $attempt->answers : [];
            foreach ($answers as $ans) {
                $qId = $ans['question_id'] ?? null;
                if (!$qId) continue;

                $q = QuizQuestion::withoutGlobalScopes()
                    ->select('id', 'topic_id', 'correct_index', 'category')
                    ->find($qId);
                if (!$q) continue;

                $topicKey = $q->topic_id ?? ('cat_' . $q->category);

                if (!isset($topicStats[$topicKey])) {
                    $topicStats[$topicKey] = [
                        'topic_id' => $q->topic_id,
                        'category' => $q->category,
                        'correct'  => 0,
                        'total'    => 0,
                    ];
                }
                $topicStats[$topicKey]['total']++;
                $selected = $ans['selected'] ?? -1;
                if ($selected !== -1 && (int) $selected === (int) $q->correct_index) {
                    $topicStats[$topicKey]['correct']++;
                }
            }
        }

        $mastery = collect($topicStats)->map(function ($stat, $key) {
            $pct = $stat['total'] > 0 ? round(($stat['correct'] / $stat['total']) * 100) : 0;
            return [
                'key'          => $key,
                'topic_id'     => $stat['topic_id'],
                'category'     => $stat['category'],
                'correct'      => $stat['correct'],
                'total'        => $stat['total'],
                'mastery_pct'  => $pct,
                'level'        => $pct >= 80 ? 'strong' : ($pct >= 50 ? 'moderate' : 'weak'),
            ];
        })->values()->sortByDesc('mastery_pct')->values();

        return response()->json([
            'success' => true,
            'data'    => [
                'topic_count' => $mastery->count(),
                'topics'      => $mastery,
            ],
        ]);
    }

    // =========================================================================
    // WEAK AREAS
    // =========================================================================

    /**
     * GET /api/v1/advanced-learning/weak-areas  [auth]
     * Returns topics / categories where the user's accuracy is below 50 %.
     */
    public function weakAreas(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        // Reuse masteryMap logic — get all topic stats then filter weak ones
        $attempts = TestAttempt::where('user_id', $userId)
            ->whereNotNull('answers')
            ->get(['answers', 'category']);

        $topicStats = [];

        foreach ($attempts as $attempt) {
            $answers = is_array($attempt->answers) ? $attempt->answers : [];
            foreach ($answers as $ans) {
                $qId = $ans['question_id'] ?? null;
                if (!$qId) continue;

                $q = QuizQuestion::withoutGlobalScopes()
                    ->select('id', 'topic_id', 'correct_index', 'category')
                    ->find($qId);
                if (!$q) continue;

                $topicKey = $q->topic_id ?? ('cat_' . $q->category);
                if (!isset($topicStats[$topicKey])) {
                    $topicStats[$topicKey] = [
                        'topic_id' => $q->topic_id,
                        'category' => $q->category,
                        'correct'  => 0,
                        'total'    => 0,
                    ];
                }
                $topicStats[$topicKey]['total']++;
                $selected = $ans['selected'] ?? -1;
                if ($selected !== -1 && (int) $selected === (int) $q->correct_index) {
                    $topicStats[$topicKey]['correct']++;
                }
            }
        }

        $weakAreas = collect($topicStats)
            ->filter(fn ($s) => $s['total'] >= 3)   // ignore topics with < 3 attempts
            ->map(function ($stat, $key) {
                $pct = $stat['total'] > 0 ? round(($stat['correct'] / $stat['total']) * 100) : 0;
                return [
                    'key'         => $key,
                    'topic_id'    => $stat['topic_id'],
                    'category'    => $stat['category'],
                    'correct'     => $stat['correct'],
                    'total'       => $stat['total'],
                    'accuracy_pct'=> $pct,
                    'suggestion'  => 'Review ' . ($stat['category'] ?? 'this topic') . ' — only ' . $pct . '% accuracy.',
                ];
            })
            ->filter(fn ($s) => $s['accuracy_pct'] < 50)
            ->sortBy('accuracy_pct')
            ->values();

        return response()->json([
            'success' => true,
            'data'    => [
                'weak_area_count' => $weakAreas->count(),
                'weak_areas'      => $weakAreas,
            ],
        ]);
    }

    // =========================================================================
    // PERSONALISED RECOMMENDATIONS
    // =========================================================================

    /**
     * GET /api/v1/advanced-learning/recommendations  [auth]
     * Returns up to 5 chapters the user hasn't read yet, prioritising categories
     * where they have weak test performance.
     */
    public function recommendations(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        // 1. Find categories with weak performance (accuracy < 60 %)
        $attempts = TestAttempt::where('user_id', $userId)
            ->whereNotNull('answers')
            ->get(['answers', 'category']);

        $categoryStats = [];
        foreach ($attempts as $attempt) {
            $cat = $attempt->category;
            if (!$cat) continue;
            if (!isset($categoryStats[$cat])) {
                $categoryStats[$cat] = ['correct' => 0, 'total' => 0];
            }
            foreach ((is_array($attempt->answers) ? $attempt->answers : []) as $ans) {
                $qId = $ans['question_id'] ?? null;
                if (!$qId) continue;
                $q = QuizQuestion::withoutGlobalScopes()
                    ->select('id', 'correct_index')
                    ->find($qId);
                if (!$q) continue;
                $categoryStats[$cat]['total']++;
                $selected = $ans['selected'] ?? -1;
                if ($selected !== -1 && (int) $selected === (int) $q->correct_index) {
                    $categoryStats[$cat]['correct']++;
                }
            }
        }

        // Sort categories by ascending accuracy
        $weakCategories = collect($categoryStats)
            ->map(fn ($s, $cat) => [
                'category' => $cat,
                'accuracy' => $s['total'] > 0 ? ($s['correct'] / $s['total']) : 1,
            ])
            ->sortBy('accuracy')
            ->take(3)
            ->pluck('category')
            ->toArray();

        // 2. Get unread chapters from weak categories
        $readIds = UserLearningProgress::where('user_id', $userId)
            ->pluck('learning_chapter_id')
            ->toArray();

        $chapters = LearningChapter::published()
            ->whereNotIn('id', $readIds)
            ->when(!empty($weakCategories), function ($q) use ($weakCategories) {
                // Prefer weak category chapters but fall back to any unread
                $q->whereHas('category', fn ($cq) =>
                    $cq->whereIn('slug', $weakCategories)
                );
            })
            ->with('category:id,slug,name_en,name_np')
            ->inRandomOrder()
            ->limit(5)
            ->get(['id', 'title_en', 'title_np', 'learning_category_id', 'read_time_minutes']);

        // If not enough from weak categories, top up with any unread chapters
        if ($chapters->count() < 5) {
            $extra = LearningChapter::published()
                ->whereNotIn('id', array_merge($readIds, $chapters->pluck('id')->toArray()))
                ->with('category:id,slug,name_en,name_np')
                ->inRandomOrder()
                ->limit(5 - $chapters->count())
                ->get(['id', 'title_en', 'title_np', 'learning_category_id', 'read_time_minutes']);
            $chapters = $chapters->concat($extra);
        }

        $items = $chapters->map(fn ($ch) => [
            'id'                => $ch->id,
            'title_en'          => $ch->title_en,
            'title_np'          => $ch->title_np,
            'read_time_minutes' => $ch->read_time_minutes,
            'category'          => $ch->category
                ? ['slug' => $ch->category->slug, 'name_en' => $ch->category->name_en]
                : null,
        ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'based_on_weak_categories' => $weakCategories,
                'recommendations'          => $items,
            ],
        ]);
    }
}
