<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MockTest;
use App\Models\QuizQuestion;
use App\Models\TestAttempt;
use App\Models\TestSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdaptiveTestController extends Controller
{
    // ── ELO constants ─────────────────────────────────────────────────────
    private const ELO_BASE        = 1200.0;
    private const ELO_K           = 32;          // sensitivity of each answer
    private const DIFF_MAP        = ['easy' => 0, 'medium' => 1, 'hard' => 2];
    private const DIFF_SEQUENCE   = ['easy', 'medium', 'hard'];

    // =========================================================================
    // POST /api/v1/adaptive-test/start
    // =========================================================================
    public function start(Request $request): JsonResponse
    {
        $request->validate([
            'mock_test_id' => 'required|exists:mock_tests,id',
        ]);

        $mockTest = MockTest::active()->findOrFail($request->mock_test_id);
        $userId   = $request->user()->id;

        // Prevent duplicate live adaptive sessions
        $existing = TestSession::where('user_id', $userId)
            ->where('mock_test_id', $mockTest->id)
            ->where('submitted', false)
            ->whereNotNull('adaptive_state')
            ->where('expires_at', '>', now())
            ->first();

        if ($existing) {
            $state        = $existing->adaptive_state;
            $nextQuestion = $this->pickNextQuestion(
                $mockTest->category,
                $state['current_difficulty'] ?? 'medium',
                $state['asked_ids'] ?? []
            );

            return response()->json(['success' => true, 'data' => [
                'session_token'      => $existing->session_token,
                'remaining_seconds'  => $existing->remaining_seconds,
                'elo_score'          => $state['elo_score'] ?? self::ELO_BASE,
                'questions_answered' => count($state['answers'] ?? []),
                'question'           => $nextQuestion ? $this->formatQuestion($nextQuestion) : null,
                'resumed'            => true,
            ]]);
        }

        // First question always 'medium' difficulty
        $firstQuestion = $this->pickNextQuestion($mockTest->category, 'medium', []);

        if (!$firstQuestion) {
            return response()->json([
                'success' => false,
                'message' => 'No questions available for this test category.',
            ], 404);
        }

        $session = TestSession::create([
            'user_id'         => $userId,
            'mock_test_id'    => $mockTest->id,
            'session_token'   => TestSession::generateToken(),
            'question_ids'    => [],   // not used in adaptive mode
            'adaptive_state'  => [
                'is_adaptive'          => true,
                'elo_score'            => self::ELO_BASE,
                'current_difficulty'   => 'medium',
                'asked_ids'            => [$firstQuestion->id],
                'answers'              => [],
            ],
            'started_at'      => now(),
            'expires_at'      => now()->addMinutes($mockTest->duration_minutes ?: 60),
        ]);

        return response()->json(['success' => true, 'data' => [
            'session_token'     => $session->session_token,
            'expires_at'        => $session->expires_at->toIso8601String(),
            'remaining_seconds' => $session->remaining_seconds,
            'elo_score'         => self::ELO_BASE,
            'questions_answered'=> 0,
            'total_questions'   => $mockTest->question_count,
            'question'          => $this->formatQuestion($firstQuestion),
        ]]);
    }

    // =========================================================================
    // POST /api/v1/adaptive-test/answer
    // Body: { session_token, question_id, selected }
    // Returns: { is_correct, elo_delta, new_elo, next_question | null }
    // =========================================================================
    public function submitAnswer(Request $request): JsonResponse
    {
        $request->validate([
            'session_token' => 'required|string',
            'question_id'   => 'required|integer|exists:quiz_questions,id',
            'selected'      => 'required|integer|min:-1|max:3',
        ]);

        $session = TestSession::where('session_token', $request->session_token)
            ->where('user_id', $request->user()->id)
            ->where('submitted', false)
            ->firstOrFail();

        if ($session->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'Session has expired. Please finish the test.',
                'expired' => true,
            ], 422);
        }

        $state    = $session->adaptive_state ?? [];
        $answers  = $state['answers'] ?? [];
        $askedIds = $state['asked_ids'] ?? [];
        $eloScore = (float) ($state['elo_score'] ?? self::ELO_BASE);

        // Validate not re-answering the same question
        $alreadyAnswered = collect($answers)->pluck('question_id')->contains($request->question_id);
        if ($alreadyAnswered) {
            return response()->json(['success' => false, 'message' => 'Question already answered.'], 422);
        }

        $question  = QuizQuestion::withoutGlobalScopes()->findOrFail($request->question_id);
        $selected  = (int) $request->selected;
        $isCorrect = $selected !== -1 && $selected === (int) $question->correct_index;
        $weight    = (float) ($question->difficulty_weight ?? 1.0);

        // ── ELO update ────────────────────────────────────────────────────
        // Expected score based on difficulty weight (harder questions have lower expected score)
        $expectedScore = 1.0 / (1.0 + pow(10, ($weight - 1.0)));
        $actualScore   = $isCorrect ? 1.0 : 0.0;
        $eloDelta      = self::ELO_K * ($actualScore - $expectedScore);
        $eloScore      = round($eloScore + $eloDelta, 2);

        // ── Adaptive difficulty for NEXT question ─────────────────────────
        $currentDiff = $state['current_difficulty'] ?? 'medium';
        $nextDiff    = $this->adaptDifficulty($currentDiff, $isCorrect);

        // ── Record answer ─────────────────────────────────────────────────
        $answers[] = [
            'question_id' => $question->id,
            'selected'    => $selected,
            'is_correct'  => $isCorrect,
            'difficulty'  => $question->difficulty,
            'weight'      => $weight,
            'topic_id'    => $question->topic_id,
            // Store correct for result display (hidden in normal API response)
            'correct_index'  => $question->correct_index,
            'explanation'    => $question->explanation,
        ];

        // ── Pick next question ─────────────────────────────────────────────
        $mockTest    = $session->mockTest;
        $maxQ        = $mockTest?->question_count ?? 20;
        $nextQuestion = null;

        if (count($answers) < $maxQ) {
            $nextQuestion = $this->pickNextQuestion(
                $mockTest->category,
                $nextDiff,
                array_merge($askedIds, [$question->id])
            );

            if ($nextQuestion) {
                $askedIds[] = $nextQuestion->id;
            }
        }

        // ── Persist state ─────────────────────────────────────────────────
        $session->update([
            'adaptive_state' => array_merge($state, [
                'elo_score'          => $eloScore,
                'current_difficulty' => $nextDiff,
                'asked_ids'          => $askedIds,
                'answers'            => $answers,
            ]),
        ]);

        return response()->json(['success' => true, 'data' => [
            'is_correct'         => $isCorrect,
            'correct_index'      => $question->correct_index,
            'explanation'        => $question->explanation,
            'elo_delta'          => round($eloDelta, 2),
            'new_elo'            => $eloScore,
            'questions_answered' => count($answers),
            'total_questions'    => $maxQ,
            'next_difficulty'    => $nextDiff,
            'next_question'      => $nextQuestion ? $this->formatQuestion($nextQuestion) : null,
            'test_complete'      => $nextQuestion === null || count($answers) >= $maxQ,
        ]]);
    }

    // =========================================================================
    // POST /api/v1/adaptive-test/finish
    // Body: { session_token }
    // Saves a TestAttempt with ELO score, topic breakdown, percentile
    // =========================================================================
    public function finish(Request $request): JsonResponse
    {
        $request->validate([
            'session_token' => 'required|string',
        ]);

        $session = TestSession::where('session_token', $request->session_token)
            ->where('user_id', $request->user()->id)
            ->where('submitted', false)
            ->firstOrFail();

        $state    = $session->adaptive_state ?? [];
        $answers  = $state['answers'] ?? [];
        $eloScore = (float) ($state['elo_score'] ?? self::ELO_BASE);
        $mockTest = $session->mockTest;

        if (empty($answers)) {
            return response()->json(['success' => false, 'message' => 'No answers recorded yet.'], 422);
        }

        // ── Score calculation ─────────────────────────────────────────────
        $total         = count($answers);
        $correctCount  = count(array_filter($answers, fn($a) => $a['is_correct']));
        $weightedScore = 0.0;
        $maxWeighted   = 0.0;

        foreach ($answers as $ans) {
            $w            = (float) ($ans['weight'] ?? 1.0);
            $maxWeighted += $w;
            if ($ans['is_correct']) $weightedScore += $w;
        }

        $rawPct      = $total > 0 ? round(($correctCount / $total) * 100) : 0;
        $weightedPct = $maxWeighted > 0 ? round(($weightedScore / $maxWeighted) * 100) : 0;
        $passed      = $mockTest ? ($rawPct >= $mockTest->pass_percentage) : ($rawPct >= 60);

        // ── Topic breakdown ───────────────────────────────────────────────
        $topicBreakdown = [];
        foreach ($answers as $ans) {
            $topic = $ans['topic_id'] ?? 'general';
            if (!isset($topicBreakdown[$topic])) {
                $topicBreakdown[$topic] = ['correct' => 0, 'total' => 0, 'accuracy' => null];
            }
            $topicBreakdown[$topic]['total']++;
            if ($ans['is_correct']) $topicBreakdown[$topic]['correct']++;
        }
        foreach ($topicBreakdown as &$t) {
            $t['accuracy'] = $t['total'] > 0 ? round(($t['correct'] / $t['total']) * 100) : 0;
        }

        // ── Difficulty breakdown ──────────────────────────────────────────
        $diffBreakdown = ['easy' => ['correct'=>0,'total'=>0], 'medium' => ['correct'=>0,'total'=>0], 'hard' => ['correct'=>0,'total'=>0]];
        foreach ($answers as $ans) {
            $d = $ans['difficulty'] ?? 'medium';
            if (!isset($diffBreakdown[$d])) $d = 'medium';
            $diffBreakdown[$d]['total']++;
            if ($ans['is_correct']) $diffBreakdown[$d]['correct']++;
        }
        foreach ($diffBreakdown as &$d) {
            $d['accuracy'] = $d['total'] > 0 ? round(($d['correct'] / $d['total']) * 100) : null;
        }

        // ── Weak areas (topics < 60% accuracy) ───────────────────────────
        $weakAreas = array_keys(array_filter($topicBreakdown, fn($t) => $t['total'] > 0 && $t['accuracy'] < 60));

        // ── Percentile ────────────────────────────────────────────────────
        $percentile = null;
        if ($mockTest) {
            $allScores  = TestAttempt::where('mock_test_id', $mockTest->id)->pluck('score_percentage')->toArray();
            $allScores[] = $rawPct;
            $countBelow = count(array_filter($allScores, fn($s) => $s < $rawPct));
            $percentile = count($allScores) > 1
                ? round(($countBelow / (count($allScores) - 1)) * 100, 2)
                : 100.0;
        }

        // ── Strip hidden data before storing answers ───────────────────────
        $storedAnswers = array_map(fn($a) => [
            'question_id' => $a['question_id'],
            'selected'    => $a['selected'],
            'is_correct'  => $a['is_correct'],
            'difficulty'  => $a['difficulty'],
            'topic_id'    => $a['topic_id'] ?? null,
        ], $answers);

        // ── Save TestAttempt ──────────────────────────────────────────────
        $attempt = TestAttempt::create([
            'user_id'              => $request->user()->id,
            'mock_test_id'         => $mockTest?->id,
            'category'             => $mockTest?->category ?? 'adaptive',
            'total_questions'      => $total,
            'correct_answers'      => $correctCount,
            'negative_marks'       => 0,
            'final_score'          => $weightedPct,
            'score_percentage'     => $rawPct,
            'percentile_score'     => $percentile,
            'difficulty_breakdown' => $diffBreakdown,
            'passed'               => $passed,
            'overtime'             => $session->isExpired(),
            'time_taken_seconds'   => now()->diffInSeconds($session->started_at),
            'answers'              => $storedAnswers,
            'completed_at'         => now(),
        ]);

        // ── Mark session submitted ────────────────────────────────────────
        $session->update(['submitted' => true, 'submitted_at' => now()]);

        return response()->json(['success' => true, 'data' => [
            'attempt_id'          => $attempt->id,
            'total_questions'     => $total,
            'correct_answers'     => $correctCount,
            'score_percentage'    => $rawPct,
            'weighted_score_pct'  => $weightedPct,
            'final_elo'           => $eloScore,
            'elo_change'          => round($eloScore - self::ELO_BASE, 2),
            'passed'              => $passed,
            'percentile_score'    => $percentile,
            'topic_breakdown'     => $topicBreakdown,
            'difficulty_breakdown'=> $diffBreakdown,
            'weak_areas'          => $weakAreas,
            'time_taken_seconds'  => $attempt->time_taken_seconds,
        ]]);
    }

    // =========================================================================
    // GET /api/v1/adaptive-test/history
    // =========================================================================
    public function history(Request $request): JsonResponse
    {
        $attempts = TestAttempt::where('user_id', $request->user()->id)
            ->whereHas('mockTest', fn($q) => $q->where('id', '>', 0))
            ->whereNotNull('difficulty_breakdown')
            ->with('mockTest:id,title,category,question_count,duration_minutes')
            ->latest('completed_at')
            ->limit(30)
            ->get()
            ->map(fn($a) => [
                'attempt_id'          => $a->id,
                'mock_test'           => $a->mockTest ? [
                    'id'       => $a->mockTest->id,
                    'title'    => $a->mockTest->title,
                    'category' => $a->mockTest->category,
                ] : null,
                'score_percentage'    => $a->score_percentage,
                'weighted_score_pct'  => $a->final_score,
                'percentile_score'    => $a->percentile_score,
                'correct_answers'     => $a->correct_answers,
                'total_questions'     => $a->total_questions,
                'passed'              => $a->passed,
                'difficulty_breakdown'=> $a->difficulty_breakdown,
                'time_taken_seconds'  => $a->time_taken_seconds,
                'completed_at'        => $a->completed_at?->toIso8601String(),
            ]);

        return response()->json(['success' => true, 'data' => $attempts]);
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /** Pick the next question avoiding already-asked ones */
    private function pickNextQuestion(string $category, string $difficulty, array $excludeIds): ?QuizQuestion
    {
        $q = QuizQuestion::active()
            ->where('category', $category)
            ->where('difficulty', $difficulty)
            ->when(!empty($excludeIds), fn($query) => $query->whereNotIn('id', $excludeIds))
            ->inRandomOrder()
            ->first();

        // Fallback: any difficulty, just not already asked
        if (!$q) {
            $q = QuizQuestion::active()
                ->where('category', $category)
                ->when(!empty($excludeIds), fn($query) => $query->whereNotIn('id', $excludeIds))
                ->inRandomOrder()
                ->first();
        }

        return $q;
    }

    /** Move difficulty up on correct, down on wrong */
    private function adaptDifficulty(string $current, bool $isCorrect): string
    {
        $idx = self::DIFF_MAP[$current] ?? 1;
        $idx = $isCorrect ? min($idx + 1, 2) : max($idx - 1, 0);
        return self::DIFF_SEQUENCE[$idx];
    }

    /** Format a question for API response (no correct index / explanation) */
    private function formatQuestion(QuizQuestion $q): array
    {
        return [
            'id'          => $q->id,
            'question'    => $q->question,
            'question_np' => $q->question_np,
            'options'     => $q->options,
            'options_np'  => $q->options_np,
            'difficulty'  => $q->difficulty,
            'weight'      => $q->difficulty_weight,
            'topic_id'    => $q->topic_id,
            'image_url'   => $q->image_url,
        ];
    }
}
