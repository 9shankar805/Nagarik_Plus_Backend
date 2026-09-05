<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TestAttempt;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    /**
     * GET /api/v1/learning/analytics
     * Returns Advanced AI Analytics & Percentile Scoring
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        // Fetch all test attempts for the user
        $attempts = TestAttempt::where('user_id', $userId)->get();

        $totalQuestions = 0;
        $correctAnswers = 0;
        $topics = [];

        foreach ($attempts as $attempt) {
            $totalQuestions += $attempt->total_questions;
            $correctAnswers += $attempt->correct_answers;

            // In a real scenario, we'd break down by topic based on $attempt->answers
            // For now, we simulate topic breakdown if answers are available
            if (is_array($attempt->answers)) {
                foreach ($attempt->answers as $ans) {
                    $category = $ans['category'] ?? 'General';
                    if (!isset($topics[$category])) {
                        $topics[$category] = ['total' => 0, 'correct' => 0];
                    }
                    $topics[$category]['total']++;
                    if (isset($ans['selected']) && isset($ans['correct_index']) && $ans['selected'] === $ans['correct_index']) {
                        $topics[$category]['correct']++;
                    }
                }
            }
        }

        $overallAccuracy = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;

        // Determine strengths and weaknesses
        $strengths = [];
        $weaknesses = [];
        foreach ($topics as $cat => $stats) {
            $accuracy = $stats['total'] > 0 ? ($stats['correct'] / $stats['total']) * 100 : 0;
            if ($accuracy >= 70) {
                $strengths[] = $cat;
            } else {
                $weaknesses[] = $cat;
            }
        }

        // Calculate percentile
        // Simulating percentile by getting all users' average scores
        $allUsersAvg = TestAttempt::selectRaw('user_id, SUM(correct_answers)/SUM(total_questions) as avg_score')
            ->groupBy('user_id')
            ->having('avg_score', '<', $overallAccuracy / 100)
            ->count();
            
        $totalUsersWithTests = TestAttempt::distinct('user_id')->count();
        $percentile = $totalUsersWithTests > 1 
            ? round(($allUsersAvg / ($totalUsersWithTests - 1)) * 100, 2) 
            : 100;

        return response()->json([
            'success' => true,
            'data' => [
                'total_tests_taken' => $attempts->count(),
                'overall_accuracy_percentage' => $overallAccuracy,
                'percentile' => $percentile,
                'strengths' => $strengths,
                'weaknesses' => $weaknesses,
                'topic_breakdown' => $topics,
            ]
        ]);
    }
}
