<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionAttempt;
use App\Models\CompetitionRegistration;
use App\Models\LeaderboardEntry;
use App\Models\LearningCategory;
use App\Models\LearningChapter;
use App\Models\MockTest;
use App\Models\QuizQuestion;
use App\Models\RoadSign;
use App\Models\Short;
use App\Models\TestAttempt;
use App\Models\TestSession;
use App\Models\UserBookmark;
use App\Models\UserLearningProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LearningController extends Controller
{
    // =========================================================================
    // CATEGORIES
    // =========================================================================

    /** GET /api/v1/learning/categories */
    public function categories(Request $request): JsonResponse
    {
        $categories = LearningCategory::active()->ordered()
            ->withCount(['publishedChapters as chapter_count', 'mockTests as mock_test_count'])
            ->get()
            ->map(fn($c) => [
                'id'            => $c->id,
                'slug'          => $c->slug,
                'name_en'       => $c->name_en,
                'name_np'       => $c->name_np,
                'description_en'=> $c->description_en,
                'description_np'=> $c->description_np,
                'icon'          => $c->icon,
                'color_code'    => $c->color_code,
                'banner_url'    => $c->banner_url,
                'chapter_count' => $c->chapter_count,
                'mock_test_count'=> $c->mock_test_count,
            ]);

        return response()->json(['success' => true, 'data' => $categories]);
    }

    /** GET /api/v1/learning/categories/{slug} */
    public function category(string $slug): JsonResponse
    {
        $category = LearningCategory::active()->where('slug', $slug)->firstOrFail();
        $chapters = $category->publishedChapters()
            ->select('id', 'title_en', 'title_np', 'summary_en', 'summary_np',
                     'image_url', 'read_time_minutes', 'display_order')
            ->get();

        $mockTests = $category->mockTests()->active()
            ->select('id', 'title', 'title_np', 'description', 'question_count',
                     'duration_minutes', 'pass_percentage', 'negative_marking', 'is_featured')
            ->get();

        return response()->json(['success' => true, 'data' => [
            'category'   => $category,
            'chapters'   => $chapters,
            'mock_tests' => $mockTests,
        ]]);
    }

    // =========================================================================
    // CHAPTERS / STUDY MATERIAL
    // =========================================================================

    /** GET /api/v1/learning/chapters */
    public function chapters(Request $request): JsonResponse
    {
        $query = LearningChapter::published()->ordered()
            ->select('id', 'learning_category_id', 'title_en', 'title_np',
                     'summary_en', 'summary_np', 'image_url', 'read_time_minutes', 'display_order');

        if ($request->category_slug) {
            $cat = LearningCategory::where('slug', $request->category_slug)->first();
            if ($cat) $query->where('learning_category_id', $cat->id);
        }
        if ($request->category_id) {
            $query->where('learning_category_id', $request->category_id);
        }

        $chapters = $query->with('category:id,slug,name_en,name_np,icon,color_code')->paginate(20);

        // Attach read status for authenticated users
        $readIds = [];
        if ($request->user()) {
            $readIds = UserLearningProgress::where('user_id', $request->user()->id)
                ->pluck('learning_chapter_id')->toArray();
        }

        $chapters->getCollection()->transform(fn($c) => array_merge($c->toArray(), [
            'is_read' => in_array($c->id, $readIds),
        ]));

        return response()->json(['success' => true, 'data' => $chapters]);
    }

    /** GET /api/v1/learning/chapters/{id} */
    public function chapter(Request $request, int $id): JsonResponse
    {
        $chapter = LearningChapter::published()->with('category:id,slug,name_en,name_np')->findOrFail($id);

        $isRead = false;
        $isBookmarked = false;
        if ($request->user()) {
            $isRead = UserLearningProgress::where('user_id', $request->user()->id)
                ->where('learning_chapter_id', $id)->exists();
            $isBookmarked = UserBookmark::where('user_id', $request->user()->id)
                ->where('bookmarkable_id', $id)
                ->where('bookmarkable_type', LearningChapter::class)->exists();
        }

        return response()->json(['success' => true, 'data' => array_merge($chapter->toArray(), [
            'is_read'       => $isRead,
            'is_bookmarked' => $isBookmarked,
        ])]);
    }

    // =========================================================================
    // MOCK TESTS (public listing)
    // =========================================================================

    /** GET /api/v1/learning/mock-tests */
    public function mockTests(Request $request): JsonResponse
    {
        $query = MockTest::active();
        if ($request->category_slug) {
            $cat = LearningCategory::where('slug', $request->category_slug)->first();
            if ($cat) $query->where('learning_category_id', $cat->id);
        }
        if ($request->category) {
            $query->byCategory($request->category);
        }
        if ($request->featured) {
            $query->featured();
        }

        $tests = $query->with('learningCategory:id,slug,name_en,name_np,icon,color_code')
            ->select('id', 'learning_category_id', 'title', 'title_np', 'description',
                     'category', 'question_count', 'duration_minutes', 'pass_percentage',
                     'negative_marking', 'is_featured')
            ->latest()
            ->paginate(15);

        return response()->json(['success' => true, 'data' => $tests]);
    }

    /** GET /api/v1/learning/mock-tests/{id} */
    public function mockTest(int $id): JsonResponse
    {
        $test = MockTest::active()
            ->with('learningCategory:id,slug,name_en,name_np')
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $test]);
    }

    // =========================================================================
    // TIMED MOCK TEST SESSION
    // =========================================================================

    /** POST /api/v1/learning/mock-tests/{id}/start  [auth] */
    public function startMockTest(Request $request, int $id): JsonResponse
    {
        $mockTest = MockTest::active()->findOrFail($id);

        // Prevent duplicate active sessions
        $existing = TestSession::where('user_id', $request->user()->id)
            ->where('mock_test_id', $id)
            ->where('submitted', false)
            ->where('expires_at', '>', now())
            ->first();

        if ($existing) {
            $questions = QuizQuestion::active()
                ->whereIn('id', $existing->question_ids)
                ->orderByRaw('FIELD(id, ' . implode(',', $existing->question_ids) . ')')
                ->get()
                ->map(fn($q) => [
                    'id'         => $q->id,
                    'question'   => $q->question,
                    'question_np'=> $q->question_np,
                    'options'    => $q->options,
                    'options_np' => $q->options_np,
                    'difficulty' => $q->difficulty,
                    'image_url'  => $q->image_url,
                ]);

            return response()->json(['success' => true, 'data' => [
                'session_token'     => $existing->session_token,
                'remaining_seconds' => $existing->remaining_seconds,
                'expires_at'        => $existing->expires_at->toIso8601String(),
                'questions'         => $questions,
                'negative_marking'  => $mockTest->negative_marking,
                'negative_value'    => $mockTest->negative_value,
            ]]);
        }

        // Build question set — fixed or random
        if ($mockTest->use_fixed_questions) {
            $questionIds = $mockTest->fixedQuestions()->pluck('quiz_questions.id')->toArray();
            // Fallback to random if fixed set is empty
            if (empty($questionIds)) {
                $questionIds = QuizQuestion::active()
                    ->when($mockTest->category, fn($q) => $q->where('category', $mockTest->category))
                    ->inRandomOrder()
                    ->limit($mockTest->question_count)
                    ->pluck('id')
                    ->toArray();
            }
        } else {
            $questionIds = QuizQuestion::active()
                ->when($mockTest->category, fn($q) => $q->where('category', $mockTest->category))
                ->inRandomOrder()
                ->limit($mockTest->question_count)
                ->pluck('id')
                ->toArray();
        }

        $session = TestSession::create([
            'user_id'       => $request->user()->id,
            'mock_test_id'  => $mockTest->id,
            'session_token' => TestSession::generateToken(),
            'question_ids'  => $questionIds,
            'started_at'    => now(),
            'expires_at'    => now()->addMinutes($mockTest->duration_minutes),
        ]);

        $questions = QuizQuestion::active()
            ->whereIn('id', $questionIds)
            ->orderByRaw('FIELD(id, ' . implode(',', $questionIds) . ')')
            ->get()
            ->map(fn($q) => [
                'id'         => $q->id,
                'question'   => $q->question,
                'question_np'=> $q->question_np,
                'options'    => $q->options,
                'options_np' => $q->options_np,
                'difficulty' => $q->difficulty,
                'image_url'  => $q->image_url,
            ]);

        return response()->json(['success' => true, 'data' => [
            'session_token'    => $session->session_token,
            'remaining_seconds'=> $session->remaining_seconds,
            'expires_at'       => $session->expires_at->toIso8601String(),
            'questions'        => $questions,
            'negative_marking' => $mockTest->negative_marking,
            'negative_value'   => $mockTest->negative_value,
        ]], 201);
    }

    // =========================================================================
    // SUBMIT ANSWERS (mock test or practice)
    // =========================================================================

    /** POST /api/v1/learning/submit  [auth] */
    public function submitAnswers(Request $request): JsonResponse
    {
        $data = $request->validate([
            'session_token'              => 'nullable|string|size:64',
            'category'                   => 'required_without:session_token|string',
            'answers'                    => 'required|array|min:1',
            'answers.*.question_id'      => 'required|integer|exists:quiz_questions,id',
            'answers.*.selected'         => 'required|integer|min:-1|max:3', // -1 = unattempted
            'time_taken_seconds'         => 'nullable|integer|min:0',
        ]);

        // Resolve session & mock test
        $session  = null;
        $mockTest = null;
        $overtime = false;
        $category = $data['category'] ?? 'practice';

        if (!empty($data['session_token'])) {
            $session = TestSession::where('session_token', $data['session_token'])
                ->where('user_id', $request->user()->id)
                ->where('submitted', false)
                ->firstOrFail();

            if ($session->isExpired()) {
                $overtime = true;
            }

            $mockTest = $session->mockTest;
            $category = $mockTest->category ?? $category;

            // Mark session submitted
            $session->update(['submitted' => true, 'submitted_at' => now()]);
        }

        // Fetch questions
        $questionIds = array_column($data['answers'], 'question_id');
        $questions   = QuizQuestion::withoutGlobalScopes()
            ->whereIn('id', $questionIds)
            ->get()->keyBy('id');

        $correctCount    = 0;
        $wrongCount      = 0;
        $unattemptedCount = 0;
        $results         = [];

        foreach ($data['answers'] as $answer) {
            $q          = $questions[$answer['question_id']] ?? null;
            if (!$q) continue;

            $selected  = (int) $answer['selected'];
            if ($selected === -1) {
                $unattemptedCount++;
                $isCorrect = false;
            } else {
                $isCorrect = $selected === $q->correct_index;
                $isCorrect ? $correctCount++ : $wrongCount++;
            }

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

        // Negative marking
        $negativeMarks = 0;
        $finalScore    = $correctCount;
        if ($mockTest && $mockTest->negative_marking) {
            [$finalScore, $negativeMarks] = $mockTest->calculateScore($correctCount, $wrongCount);
        }

        $percentage = $total > 0 ? round(($finalScore / $total) * 100) : 0;
        $passed     = $percentage >= ($mockTest->pass_percentage ?? 60);

        // Save attempt
        $attempt = TestAttempt::create([
            'user_id'            => $request->user()->id,
            'mock_test_id'       => $mockTest?->id,
            'category'           => $category,
            'total_questions'    => $total,
            'correct_answers'    => $correctCount,
            'negative_marks'     => $negativeMarks,
            'final_score'        => $finalScore,
            'score_percentage'   => $percentage,
            'passed'             => $passed,
            'overtime'           => $overtime,
            'time_taken_seconds' => $data['time_taken_seconds'] ?? null,
            'answers'            => $data['answers'],
            'completed_at'       => now(),
        ]);

        // ── Real-time percentile + difficulty breakdown ─────────────────────
        $percentileScore    = null;
        $difficultyBreakdown = null;

        if ($mockTest) {
            // Percentile: count attempts with lower score / (total - 1)
            $allScores  = TestAttempt::where('mock_test_id', $mockTest->id)
                ->whereNotNull('score_percentage')
                ->pluck('score_percentage')
                ->toArray();
            $totalCount = count($allScores);
            $countBelow = count(array_filter($allScores, fn($s) => $s < $percentage));
            $percentileScore = $totalCount > 1
                ? round(($countBelow / ($totalCount - 1)) * 100, 2)
                : 100.00;

            // Difficulty breakdown from the answered questions
            $breakdown = [
                'easy'   => ['correct' => 0, 'total' => 0, 'accuracy' => null],
                'medium' => ['correct' => 0, 'total' => 0, 'accuracy' => null],
                'hard'   => ['correct' => 0, 'total' => 0, 'accuracy' => null],
            ];
            foreach ($results as $r) {
                $diff = $r['difficulty'] ?? 'medium';
                if (!isset($breakdown[$diff])) $diff = 'medium';
                $breakdown[$diff]['total']++;
                if ($r['is_correct']) $breakdown[$diff]['correct']++;
            }
            foreach ($breakdown as $diff => &$d) {
                $d['accuracy'] = $d['total'] > 0
                    ? round(($d['correct'] / $d['total']) * 100)
                    : null;
            }
            $difficultyBreakdown = $breakdown;

            $attempt->update([
                'percentile_score'    => $percentileScore,
                'difficulty_breakdown'=> $difficultyBreakdown,
            ]);
        }

        return response()->json(['success' => true, 'data' => [
            'attempt_id'          => $attempt->id,
            'total_questions'     => $total,
            'correct_answers'     => $correctCount,
            'wrong_answers'       => $wrongCount,
            'unattempted'         => $unattemptedCount,
            'negative_marks'      => $negativeMarks,
            'final_score'         => $finalScore,
            'score_percentage'    => $percentage,
            'percentile_score'    => $percentileScore,
            'difficulty_breakdown'=> $difficultyBreakdown,
            'passed'              => $passed,
            'overtime'            => $overtime,
            'results'             => $results,
        ]]);
    }

    // =========================================================================
    // COMPETITIONS
    // =========================================================================

    /** GET /api/v1/learning/competitions  (public) */
    public function competitions(Request $request): JsonResponse
    {
        $query = Competition::visible()
            ->with('category:id,slug,name_en,name_np,icon,color_code')
            ->select('id', 'learning_category_id', 'title', 'title_np', 'description',
                     'banner_url', 'starts_at', 'ends_at', 'registration_close_at',
                     'max_participants', 'entry_fee', 'is_free', 'prize_pool', 'status',
                     'prize_distribution');

        if ($request->category_slug) {
            $cat = LearningCategory::where('slug', $request->category_slug)->first();
            if ($cat) $query->where('learning_category_id', $cat->id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $competitions = $query->orderBy('starts_at')->paginate(10);

        $userId = $request->user()?->id;
        $competitions->getCollection()->transform(function ($c) use ($userId) {
            $data = $c->toArray();
            $data['participant_count'] = $c->registrations()->where('payment_status', 'paid')->count();
            $data['is_registered']     = $userId ? $c->isUserRegistered($userId) : false;
            $data['registration_open'] = $c->isRegistrationOpen();
            return $data;
        });

        return response()->json(['success' => true, 'data' => $competitions]);
    }

    /** GET /api/v1/learning/competitions/{id}  (public) */
    public function competition(Request $request, int $id): JsonResponse
    {
        $competition = Competition::visible()
            ->with(['category:id,slug,name_en,name_np', 'mockTest:id,title,question_count,duration_minutes'])
            ->findOrFail($id);

        $userId = $request->user()?->id;

        return response()->json(['success' => true, 'data' => array_merge($competition->toArray(), [
            'participant_count' => $competition->registrations()->where('payment_status', 'paid')->count(),
            'is_registered'     => $userId ? $competition->isUserRegistered($userId) : false,
            'has_attempted'     => $userId ? $competition->hasUserAttempted($userId) : false,
            'registration_open' => $competition->isRegistrationOpen(),
        ])]);
    }

    /** POST /api/v1/learning/competitions/{id}/register  [auth] */
    public function registerForCompetition(Request $request, int $id): JsonResponse
    {
        $competition = Competition::findOrFail($id);

        if (!$competition->isRegistrationOpen()) {
            return response()->json(['success' => false, 'message' => 'Registration is not open for this competition.'], 422);
        }
        if ($competition->isUserRegistered($request->user()->id)) {
            return response()->json(['success' => false, 'message' => 'You are already registered.'], 422);
        }

        $paymentStatus = $competition->is_free ? 'paid' : 'pending';

        $registration = CompetitionRegistration::create([
            'competition_id' => $competition->id,
            'user_id'        => $request->user()->id,
            'registered_at'  => now(),
            'payment_status' => $paymentStatus,
            'payment_method' => $competition->is_free ? 'free' : null,
            'paid_at'        => $competition->is_free ? now() : null,
        ]);

        return response()->json(['success' => true, 'data' => [
            'registration_id' => $registration->id,
            'payment_required'=> !$competition->is_free,
            'entry_fee'       => $competition->entry_fee,
            'message'         => $competition->is_free
                ? 'Registered successfully!'
                : 'Registered. Please complete payment to confirm your seat.',
        ]], 201);
    }

    /** POST /api/v1/learning/competitions/{id}/start  [auth] */
    public function startCompetition(Request $request, int $id): JsonResponse
    {
        $competition = Competition::findOrFail($id);

        if ($competition->status !== Competition::STATUS_ONGOING) {
            return response()->json(['success' => false, 'message' => 'Competition is not ongoing.'], 422);
        }
        if (!$competition->isUserRegistered($request->user()->id)) {
            return response()->json(['success' => false, 'message' => 'You are not registered for this competition.'], 403);
        }

        $reg = CompetitionRegistration::where('competition_id', $id)
            ->where('user_id', $request->user()->id)
            ->first();
        if (!$reg->isPaid()) {
            return response()->json(['success' => false, 'message' => 'Payment not confirmed.'], 403);
        }
        if ($competition->hasUserAttempted($request->user()->id)) {
            return response()->json(['success' => false, 'message' => 'You have already attempted this competition.'], 422);
        }

        // Check for active competition session
        $existing = TestSession::where('user_id', $request->user()->id)
            ->where('mock_test_id', $competition->mock_test_id)
            ->where('submitted', false)
            ->where('expires_at', '>', now())
            ->first();

        if ($existing) {
            $questionIds = $existing->question_ids;
        } else {
            $mockTest    = $competition->mockTest;
            $questionIds = QuizQuestion::active()
                ->when($mockTest->category, fn($q) => $q->where('category', $mockTest->category))
                ->inRandomOrder()
                ->limit($mockTest->question_count)
                ->pluck('id')->toArray();

            $existing = TestSession::create([
                'user_id'       => $request->user()->id,
                'mock_test_id'  => $mockTest->id,
                'session_token' => TestSession::generateToken(),
                'question_ids'  => $questionIds,
                'started_at'    => now(),
                'expires_at'    => now()->addMinutes($mockTest->duration_minutes),
            ]);
        }

        $questions = QuizQuestion::active()
            ->whereIn('id', $questionIds)
            ->orderByRaw('FIELD(id, ' . implode(',', $questionIds) . ')')
            ->get()
            ->map(fn($q) => [
                'id'          => $q->id,
                'question'    => $q->question,
                'question_np' => $q->question_np,
                'options'     => $q->options,
                'options_np'  => $q->options_np,
                'difficulty'  => $q->difficulty,
                'image_url'   => $q->image_url,
            ]);

        return response()->json(['success' => true, 'data' => [
            'session_token'    => $existing->session_token,
            'remaining_seconds'=> $existing->remaining_seconds,
            'expires_at'       => $existing->expires_at->toIso8601String(),
            'questions'        => $questions,
            'negative_marking' => $competition->mockTest->negative_marking,
            'negative_value'   => $competition->mockTest->negative_value,
        ]]);
    }

    /** POST /api/v1/learning/competitions/{id}/submit  [auth] */
    public function submitCompetition(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'session_token'          => 'required|string|size:64',
            'answers'                => 'required|array|min:1',
            'answers.*.question_id'  => 'required|integer|exists:quiz_questions,id',
            'answers.*.selected'     => 'required|integer|min:-1|max:3',
            'time_taken_seconds'     => 'nullable|integer|min:0',
        ]);

        $competition = Competition::findOrFail($id);

        if ($competition->hasUserAttempted($request->user()->id)) {
            return response()->json(['success' => false, 'message' => 'Already submitted.'], 422);
        }

        $session = TestSession::where('session_token', $data['session_token'])
            ->where('user_id', $request->user()->id)
            ->where('submitted', false)
            ->firstOrFail();

        $overtime = $session->isExpired();
        $session->update(['submitted' => true, 'submitted_at' => now()]);

        $mockTest    = $competition->mockTest;
        $questionIds = array_column($data['answers'], 'question_id');
        $questions   = QuizQuestion::withoutGlobalScopes()->whereIn('id', $questionIds)->get()->keyBy('id');

        $correctCount = $wrongCount = $unattempted = 0;
        $results = [];

        foreach ($data['answers'] as $answer) {
            $q = $questions[$answer['question_id']] ?? null;
            if (!$q) continue;
            $selected = (int) $answer['selected'];
            if ($selected === -1) { $unattempted++; }
            elseif ($selected === $q->correct_index) { $correctCount++; }
            else { $wrongCount++; }
            $results[] = [
                'question_id'   => $q->id, 'selected' => $selected,
                'correct_index' => $q->correct_index,
                'is_correct'    => $selected !== -1 && $selected === $q->correct_index,
                'explanation'   => $q->explanation, 'explanation_np' => $q->explanation_np,
            ];
        }

        $total = count($data['answers']);
        [$finalScore, $negativeMarks] = $mockTest->calculateScore($correctCount, $wrongCount);
        $percentage = $total > 0 ? round(($finalScore / $total) * 100) : 0;
        $passed     = $percentage >= $mockTest->pass_percentage;

        $attempt = DB::transaction(function () use (
            $request, $competition, $mockTest, $data, $total,
            $correctCount, $wrongCount, $unattempted,
            $negativeMarks, $finalScore, $percentage, $passed, $overtime, $results
        ) {
            $testAttempt = TestAttempt::create([
                'user_id'            => $request->user()->id,
                'mock_test_id'       => $mockTest->id,
                'competition_id'     => $competition->id,
                'is_competition'     => true,
                'category'           => $mockTest->category,
                'total_questions'    => $total,
                'correct_answers'    => $correctCount,
                'negative_marks'     => $negativeMarks,
                'final_score'        => $finalScore,
                'score_percentage'   => $percentage,
                'passed'             => $passed,
                'overtime'           => $overtime,
                'time_taken_seconds' => $data['time_taken_seconds'] ?? null,
                'answers'            => $data['answers'],
                'completed_at'       => now(),
            ]);

            CompetitionAttempt::create([
                'competition_id'     => $competition->id,
                'user_id'            => $request->user()->id,
                'test_attempt_id'    => $testAttempt->id,
                'raw_score'          => $finalScore,
                'score_percentage'   => $percentage,
                'correct_answers'    => $correctCount,
                'wrong_answers'      => $wrongCount,
                'unattempted'        => $unattempted,
                'time_taken_seconds' => $data['time_taken_seconds'] ?? null,
            ]);

            return $testAttempt;
        });

        return response()->json(['success' => true, 'data' => [
            'attempt_id'       => $attempt->id,
            'total_questions'  => $total,
            'correct_answers'  => $correctCount,
            'wrong_answers'    => $wrongCount,
            'unattempted'      => $unattempted,
            'negative_marks'   => $negativeMarks,
            'final_score'      => $finalScore,
            'score_percentage' => $percentage,
            'passed'           => $passed,
            'overtime'         => $overtime,
            'results'          => $results,
            'message'          => 'Submission recorded. Leaderboard will be computed after the competition ends.',
        ]]);
    }

    /** GET /api/v1/learning/competitions/{id}/leaderboard  (public) */
    public function leaderboard(int $id): JsonResponse
    {
        $competition = Competition::findOrFail($id);

        $entries = LeaderboardEntry::where('competition_id', $id)
            ->with('user:id,name,avatar')
            ->orderBy('rank')
            ->limit(100)
            ->get()
            ->map(fn($e) => [
                'rank'               => $e->rank,
                'user'               => $e->user ? [
                    'id'     => $e->user->id,
                    'name'   => $e->user->name,
                    'avatar' => $e->user->avatar,
                ] : null,
                'score_percentage'   => $e->score_percentage,
                'correct_answers'    => $e->correct_answers,
                'time_taken_seconds' => $e->time_taken_seconds,
                'prize_won'          => $e->prize_won,
            ]);

        return response()->json(['success' => true, 'data' => [
            'competition_id'    => $competition->id,
            'competition_title' => $competition->title,
            'status'            => $competition->status,
            'prize_pool'        => $competition->prize_pool,
            'leaderboard'       => $entries,
        ]]);
    }

    /** GET /api/v1/learning/competitions/{id}/result  [auth] */
    public function myCompetitionResult(Request $request, int $id): JsonResponse
    {
        $entry = LeaderboardEntry::where('competition_id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        $attempt = CompetitionAttempt::where('competition_id', $id)
            ->where('user_id', $request->user()->id)
            ->with('testAttempt')
            ->first();

        if (!$attempt) {
            return response()->json(['success' => false, 'message' => 'No attempt found.'], 404);
        }

        return response()->json(['success' => true, 'data' => [
            'rank'               => $entry?->rank,
            'prize_won'          => $entry?->prize_won ?? 0,
            'score_percentage'   => $attempt->score_percentage,
            'correct_answers'    => $attempt->correct_answers,
            'wrong_answers'      => $attempt->wrong_answers,
            'unattempted'        => $attempt->unattempted,
            'time_taken_seconds' => $attempt->time_taken_seconds,
        ]]);
    }

    /** GET /api/v1/learning/competitions/my  [auth] */
    public function myCompetitions(Request $request): JsonResponse
    {
        $attempts = CompetitionAttempt::where('user_id', $request->user()->id)
            ->with('competition:id,title,starts_at,ends_at,prize_pool,status')
            ->latest()
            ->paginate(15);

        return response()->json(['success' => true, 'data' => $attempts]);
    }

    // =========================================================================
    // PROGRESS & BOOKMARKS
    // =========================================================================

    /** POST /api/v1/learning/chapters/{id}/read  [auth] */
    public function markChapterRead(Request $request, int $id): JsonResponse
    {
        LearningChapter::published()->findOrFail($id);

        UserLearningProgress::firstOrCreate([
            'user_id'            => $request->user()->id,
            'learning_chapter_id'=> $id,
        ], ['read_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Chapter marked as read.']);
    }

    /** GET /api/v1/learning/progress  [auth] */
    public function progress(Request $request): JsonResponse
    {
        $progress = UserLearningProgress::where('user_id', $request->user()->id)
            ->with('chapter:id,learning_category_id,title_en,title_np')
            ->latest('read_at')
            ->get();

        $totalChapters = LearningChapter::published()->count();
        $readCount     = $progress->count();

        return response()->json(['success' => true, 'data' => [
            'total_chapters'  => $totalChapters,
            'chapters_read'   => $readCount,
            'completion_pct'  => $totalChapters > 0 ? round(($readCount / $totalChapters) * 100) : 0,
            'progress'        => $progress,
        ]]);
    }

    /** POST /api/v1/learning/bookmarks  [auth] */
    public function toggleBookmark(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => 'required|in:chapter,question',
            'id'   => 'required|integer',
        ]);

        $type = $data['type'] === 'chapter' ? LearningChapter::class : QuizQuestion::class;

        $existing = UserBookmark::where('user_id', $request->user()->id)
            ->where('bookmarkable_id', $data['id'])
            ->where('bookmarkable_type', $type)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['success' => true, 'bookmarked' => false, 'message' => 'Bookmark removed.']);
        }

        UserBookmark::create([
            'user_id'          => $request->user()->id,
            'bookmarkable_id'  => $data['id'],
            'bookmarkable_type'=> $type,
        ]);

        return response()->json(['success' => true, 'bookmarked' => true, 'message' => 'Bookmarked.']);
    }

    /** GET /api/v1/learning/bookmarks  [auth] */
    public function bookmarks(Request $request): JsonResponse
    {
        $bookmarks = UserBookmark::where('user_id', $request->user()->id)
            ->with('bookmarkable')
            ->latest()
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $bookmarks]);
    }

    // =========================================================================
    // LEARNING STATS
    // =========================================================================

    /** GET /api/v1/learning/stats  [auth] */
    public function stats(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $attempts = TestAttempt::where('user_id', $userId);
        $total    = (clone $attempts)->count();
        $passed   = (clone $attempts)->where('passed', true)->count();
        $avgScore = (clone $attempts)->avg('score_percentage') ?? 0;
        $bestScore= (clone $attempts)->max('score_percentage') ?? 0;

        $chaptersRead   = UserLearningProgress::where('user_id', $userId)->count();
        $totalChapters  = LearningChapter::published()->count();
        $competitions   = CompetitionAttempt::where('user_id', $userId)->count();
        $prizeWon       = CompetitionAttempt::where('user_id', $userId)->sum('prize_won');

        $byCategory = TestAttempt::where('user_id', $userId)
            ->select('category', DB::raw('COUNT(*) as attempts'), DB::raw('AVG(score_percentage) as avg_score'))
            ->groupBy('category')
            ->get();

        return response()->json(['success' => true, 'data' => [
            'total_attempts'      => $total,
            'passed_attempts'     => $passed,
            'pass_rate'           => $total > 0 ? round(($passed / $total) * 100) : 0,
            'average_score'       => round($avgScore, 1),
            'best_score'          => $bestScore,
            'chapters_read'       => $chaptersRead,
            'total_chapters'      => $totalChapters,
            'study_completion_pct'=> $totalChapters > 0 ? round(($chaptersRead / $totalChapters) * 100) : 0,
            'competitions_entered'=> $competitions,
            'total_prize_won'     => $prizeWon,
            'by_category'         => $byCategory,
        ]]);
    }

    // =========================================================================
    // LEGACY ENDPOINTS (keep backward compatible)
    // =========================================================================

    /** GET /api/v1/learning/road-signs  (public) */
    public function roadSigns(Request $request): JsonResponse
    {
        $query = RoadSign::where('is_active', true);
        if ($request->category) {
            $query->where('category', $request->category);
        }
        $signs = $query->get()->map(fn($s) => [
            'id'         => $s->id,
            'name'       => $s->name,
            'name_np'    => $s->name_np,
            'meaning'    => $s->meaning,
            'meaning_np' => $s->meaning_np,
            'category'   => $s->category,
            'image_url'  => $s->image_url
                ? (str_starts_with($s->image_url, '/storage/') ? asset($s->image_url) : $s->image_url)
                : null,
            'color_code' => $s->color_code,
        ]);
        return response()->json(['success' => true, 'data' => $signs]);
    }

    /** GET /api/v1/learning/shorts  (public) */
    public function shorts(Request $request): JsonResponse
    {
        $query = Short::where('is_published', true);
        if ($request->category) {
            $query->where('category', $request->category);
        }
        $dbShorts = $query->latest()->get();
        if ($dbShorts->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $dbShorts]);
        }
        return response()->json(['success' => true, 'data' => []]);
    }

    /** GET /api/v1/learning/questions  (public) */
    public function questions(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category'   => 'sometimes|string',
            'difficulty' => 'sometimes|in:easy,medium,hard',
            'limit'      => 'sometimes|integer|min:5|max:50',
        ]);

        $query = QuizQuestion::active()->byCategory($data['category'] ?? 'driving_license');
        if (!empty($data['difficulty'])) {
            $query->where('difficulty', $data['difficulty']);
        }

        $questions = $query->inRandomOrder()->limit($data['limit'] ?? 25)->get()
            ->map(fn($q) => [
                'id'         => $q->id,
                'question'   => $q->question,
                'question_np'=> $q->question_np,
                'options'    => $q->options,
                'options_np' => $q->options_np,
                'difficulty' => $q->difficulty,
                'image_url'  => $q->image_url,
            ]);

        return response()->json(['success' => true, 'data' => ['questions' => $questions]]);
    }

    /** GET /api/v1/learning/history  [auth] */
    public function history(Request $request): JsonResponse
    {
        $query = TestAttempt::where('user_id', $request->user()->id);

        if ($request->category) {
            $query->where('category', $request->category);
        }
        if ($request->type === 'competition') {
            $query->where('is_competition', true);
        }

        $attempts = $query->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(fn($a) => [
                'id'               => $a->id,
                'category'         => $a->category,
                'total_questions'  => $a->total_questions,
                'correct_answers'  => $a->correct_answers,
                'negative_marks'   => $a->negative_marks,
                'final_score'      => $a->final_score,
                'score_percentage' => $a->score_percentage,
                'passed'           => $a->passed,
                'is_competition'   => $a->is_competition,
                'overtime'         => $a->overtime,
                'completed_at'     => $a->completed_at?->toDateTimeString(),
            ]);

        return response()->json(['success' => true, 'data' => $attempts]);
    }

    // =========================================================================
    // WRONG-ONLY RE-TEST
    // =========================================================================

    /**
     * POST /api/v1/learning/mock-tests/{id}/retry-wrong  [auth]
     *
     * Returns a practice-style question set containing only the questions
     * the user answered incorrectly in their most recent attempt of this test.
     * Pass ?attempt_id=X to retry a specific past attempt.
     */
    public function retryWrong(Request $request, int $id): JsonResponse
    {
        $mockTest = MockTest::active()->findOrFail($id);
        $userId   = $request->user()->id;

        $attemptQuery = TestAttempt::where('user_id', $userId)->where('mock_test_id', $id);

        $attempt = $request->attempt_id
            ? $attemptQuery->findOrFail($request->attempt_id)
            : $attemptQuery->latest()->first();

        if (!$attempt) {
            return response()->json(['success' => false, 'message' => 'No previous attempt found for this test.'], 404);
        }

        if (empty($attempt->answers)) {
            return response()->json(['success' => false, 'message' => 'No answer data found in this attempt.'], 404);
        }

        // Extract wrong + unattempted question IDs
        $wrongIds = [];
        foreach ($attempt->answers as $ans) {
            $qId      = $ans['question_id'] ?? null;
            $selected = $ans['selected'] ?? -1;
            if (!$qId) continue;
            if ((int)$selected === -1) {
                $wrongIds[] = $qId;
                continue;
            }
            $q = QuizQuestion::withoutGlobalScopes()->find($qId);
            if ($q && (int)$selected !== (int)$q->correct_index) {
                $wrongIds[] = $qId;
            }
        }

        $wrongIds = array_values(array_unique($wrongIds));

        if (empty($wrongIds)) {
            return response()->json([
                'success' => false,
                'message' => 'You got everything right in that attempt — nothing to retry! 🎉',
            ], 404);
        }

        $questions = QuizQuestion::active()
            ->whereIn('id', $wrongIds)
            ->get()
            ->map(fn($q) => [
                'id'          => $q->id,
                'question'    => $q->question,
                'question_np' => $q->question_np,
                'options'     => $q->options,
                'options_np'  => $q->options_np,
                'difficulty'  => $q->difficulty,
                'image_url'   => $q->image_url,
            ]);

        return response()->json(['success' => true, 'data' => [
            'mock_test_id'    => $mockTest->id,
            'mock_test_title' => $mockTest->title,
            'attempt_id'      => $attempt->id,
            'mode'            => 'wrong_only',
            'original_score'  => $attempt->score_percentage,
            'wrong_count'     => count($wrongIds),
            'total_count'     => count($attempt->answers ?? []),
            'questions'       => $questions,
        ]]);
    }
}
