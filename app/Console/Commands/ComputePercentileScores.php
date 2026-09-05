<?php

namespace App\Console\Commands;

use App\Models\MockTest;
use App\Models\QuizQuestion;
use App\Models\TestAttempt;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ComputePercentileScores extends Command
{
    protected $signature = 'learning:compute-percentiles
                            {--mock-test= : Only recompute for a specific mock test ID}
                            {--fresh : Recompute all, even already-computed attempts}';

    protected $description = 'Compute percentile_score and difficulty_breakdown for all test attempts';

    public function handle(): int
    {
        $this->info('Computing percentile scores...');

        $testIds = MockTest::pluck('id');

        if ($this->option('mock-test')) {
            $testIds = collect([$this->option('mock-test')]);
        }

        $total = 0;

        foreach ($testIds as $mockTestId) {
            $total += $this->processTest((int) $mockTestId);
        }

        $this->info("Done. Updated {$total} attempt(s).");
        return self::SUCCESS;
    }

    private function processTest(int $mockTestId): int
    {
        // Fetch all completed attempts for this test
        $attempts = TestAttempt::where('mock_test_id', $mockTestId)
            ->whereNotNull('score_percentage')
            ->when(!$this->option('fresh'), fn($q) => $q->whereNull('percentile_score'))
            ->get();

        if ($attempts->isEmpty()) {
            return 0;
        }

        // All scores for this test (for percentile calculation)
        $allScores = TestAttempt::where('mock_test_id', $mockTestId)
            ->whereNotNull('score_percentage')
            ->pluck('score_percentage')
            ->sort()
            ->values()
            ->toArray();

        $totalCount = count($allScores);

        // Pre-load all questions for difficulty breakdown (use withoutGlobalScopes to get correct_index)
        $allQuestionIds = $attempts->flatMap(fn($a) => collect($a->answers ?? [])->pluck('question_id'))
            ->unique()->values()->toArray();

        $questions = QuizQuestion::withoutGlobalScopes()
            ->whereIn('id', $allQuestionIds)
            ->get()
            ->keyBy('id');

        $updated = 0;

        foreach ($attempts as $attempt) {
            // ── Percentile ─────────────────────────────────────────────────
            // Count how many attempts scored strictly below this one
            $countBelow = count(array_filter($allScores, fn($s) => $s < $attempt->score_percentage));
            $percentile = $totalCount > 1
                ? round(($countBelow / ($totalCount - 1)) * 100, 2)
                : 100.00;

            // ── Difficulty breakdown ────────────────────────────────────────
            $breakdown = [
                'easy'   => ['correct' => 0, 'total' => 0],
                'medium' => ['correct' => 0, 'total' => 0],
                'hard'   => ['correct' => 0, 'total' => 0],
            ];

            foreach ($attempt->answers ?? [] as $ans) {
                $qId      = $ans['question_id'] ?? null;
                $selected = $ans['selected'] ?? -1;
                if (!$qId) continue;

                $q = $questions[$qId] ?? null;
                if (!$q) continue;

                $diff = $q->difficulty ?? 'medium';
                if (!isset($breakdown[$diff])) $diff = 'medium';

                $breakdown[$diff]['total']++;
                if ((int)$selected !== -1 && (int)$selected === (int)$q->correct_index) {
                    $breakdown[$diff]['correct']++;
                }
            }

            // Add accuracy % per difficulty
            foreach ($breakdown as $diff => &$data) {
                $data['accuracy'] = $data['total'] > 0
                    ? round(($data['correct'] / $data['total']) * 100)
                    : null;
            }

            $attempt->update([
                'percentile_score'    => $percentile,
                'difficulty_breakdown'=> $breakdown,
            ]);

            $updated++;
        }

        return $updated;
    }
}
