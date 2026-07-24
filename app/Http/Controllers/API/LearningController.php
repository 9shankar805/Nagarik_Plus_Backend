<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MockTest;
use App\Models\QuizQuestion;
use App\Models\RoadSign;
use App\Models\Short;
use App\Models\TestAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LearningController extends Controller
{
    /**
     * Get educational shorts and video tutorials
     */
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

        $fallbackShorts = [
            [
                'id' => 1,
                'title_en' => 'Traffic Signals & Hand Signs Tutorial',
                'title_np' => 'सवारी इसारा र ट्राफिक बत्ती नियम जानकारी',
                'description_en' => 'Learn mandatory road signal rules for driving license test.',
                'description_np' => 'ड्राइभिङ लाइसेन्स परीक्षाका लागि आवश्यक सडक नियम।',
                'video_url' => 'https://www.w3schools.com/html/mov_bbb.mp4',
                'category' => 'traffic_rules',
                'duration_seconds' => 45,
                'views_count' => 1240,
                'likes_count' => 180,
                'is_published' => true,
            ],
            [
                'id' => 2,
                'title_en' => '8-Shape & Ramp Driving Trial Tips',
                'title_np' => '८ आकार र उकालो-ओरालो ट्रायल पास गर्ने तरिका',
                'description_en' => 'Step-by-step practical trial guide for two-wheelers & cars.',
                'description_np' => 'मोटरसाइकल र कार ट्रायल पास गर्ने प्रयोगात्मक जानकारी।',
                'video_url' => 'https://www.w3schools.com/html/mov_bbb.mp4',
                'category' => 'license_prep',
                'duration_seconds' => 60,
                'views_count' => 3890,
                'likes_count' => 540,
                'is_published' => true,
            ],
        ];

        return response()->json(['success' => true, 'data' => $fallbackShorts]);
    }

    /**
     * Get random quiz questions for practice
     */
    public function questions(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category'   => 'sometimes|string',
            'difficulty' => 'sometimes|in:easy,medium,hard',
            'limit'      => 'sometimes|integer|min:5|max:50',
        ]);

        $query = QuizQuestion::active()
                             ->byCategory($data['category'] ?? 'driving_license');

        if (!empty($data['difficulty'])) {
            $query->where('difficulty', $data['difficulty']);
        }

        $questions = $query->inRandomOrder()
                           ->limit($data['limit'] ?? 25)
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

        return response()->json(['success' => true, 'data' => ['questions' => $questions]]);
    }

    /**
     * Submit answers and get results
     */
    public function submitAnswers(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category'           => 'required|string',
            'answers'            => 'required|array',
            'answers.*.question_id' => 'required|integer|exists:quiz_questions,id',
            'answers.*.selected' => 'required|integer|min:0|max:3',
            'time_taken_seconds' => 'nullable|integer',
        ]);

        $questionIds = array_column($data['answers'], 'question_id');
        $questions   = QuizQuestion::withoutGlobalScopes()
                                   ->whereIn('id', $questionIds)
                                   ->get()
                                   ->keyBy('id');

        $correctCount = 0;
        $results = [];

        foreach ($data['answers'] as $answer) {
            $q          = $questions[$answer['question_id']];
            $isCorrect  = $answer['selected'] === $q->correct_index;
            if ($isCorrect) $correctCount++;

            $results[] = [
                'question_id'    => $q->id,
                'selected'       => $answer['selected'],
                'correct_index'  => $q->correct_index,
                'is_correct'     => $isCorrect,
                'explanation'    => $q->explanation,
                'explanation_np' => $q->explanation_np,
            ];
        }

        $total      = count($data['answers']);
        $percentage = $total > 0 ? round(($correctCount / $total) * 100) : 0;
        $passed     = $percentage >= 60;

        // Save attempt
        TestAttempt::create([
            'user_id'           => $request->user()->id,
            'category'          => $data['category'],
            'total_questions'   => $total,
            'correct_answers'   => $correctCount,
            'score_percentage'  => $percentage,
            'passed'            => $passed,
            'time_taken_seconds'=> $data['time_taken_seconds'] ?? null,
            'answers'           => $data['answers'],
            'completed_at'      => now(),
        ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'total_questions' => $total,
                'correct_answers' => $correctCount,
                'score_percentage'=> $percentage,
                'passed'          => $passed,
                'results'         => $results,
            ],
        ]);
    }

    /**
     * Get road signs
     */
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
            'image_url'  => $s->image_url,
            'color_code' => $s->color_code,
        ]);

        return response()->json(['success' => true, 'data' => $signs]);
    }

    /**
     * Get user's test history
     */
    public function history(Request $request): JsonResponse
    {
        $attempts = TestAttempt::where('user_id', $request->user()->id)
                               ->orderBy('created_at', 'desc')
                               ->limit(20)
                               ->get()
                               ->map(fn($a) => [
                                   'id'               => $a->id,
                                   'category'         => $a->category,
                                   'total_questions'  => $a->total_questions,
                                   'correct_answers'  => $a->correct_answers,
                                   'score_percentage' => $a->score_percentage,
                                   'passed'           => $a->passed,
                                   'completed_at'     => $a->completed_at?->toDateTimeString(),
                               ]);

        return response()->json(['success' => true, 'data' => $attempts]);
    }
}
