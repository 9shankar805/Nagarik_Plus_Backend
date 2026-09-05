<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningCategory;
use App\Models\MockTest;
use App\Models\QuizQuestion;
use App\Models\TestAttempt;
use Illuminate\Http\Request;

class AdminMockTestController extends Controller
{
    public function index(Request $request)
    {
        $query = MockTest::with('learningCategory:id,name_en,slug');

        if ($request->category_id) {
            $query->where('learning_category_id', $request->category_id);
        }
        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $mockTests  = $query->latest()->paginate(20)->withQueryString();
        $categories = LearningCategory::active()->ordered()->get();

        return view('admin.learning.mock-tests.index', compact('mockTests', 'categories'));
    }

    public function create()
    {
        $categories = LearningCategory::active()->ordered()->get();

        // Available category slugs for the legacy 'category' string field
        $categorySlugs = $categories->pluck('slug')->toArray();

        return view('admin.learning.mock-tests.create', compact('categories', 'categorySlugs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'learning_category_id' => 'required|exists:learning_categories,id',
            'title'                => 'required|string|max:255',
            'title_np'             => 'nullable|string|max:255',
            'description'          => 'nullable|string',
            'description_np'       => 'nullable|string',
            'question_count'       => 'required|integer|min:5|max:200',
            'duration_minutes'     => 'required|integer|min:5|max:300',
            'pass_percentage'      => 'required|integer|min:1|max:100',
            'negative_marking'     => 'nullable|boolean',
            'negative_value'       => 'nullable|numeric|min:0|max:1',
        ]);

        // Derive legacy category string from the linked learning category
        $category = LearningCategory::find($request->learning_category_id);

        MockTest::create([
            'learning_category_id' => $request->learning_category_id,
            'title'                => $request->title,
            'title_np'             => $request->title_np,
            'description'          => $request->description,
            'description_np'       => $request->description_np,
            'category'             => $category->slug,
            'question_count'       => $request->question_count,
            'duration_minutes'     => $request->duration_minutes,
            'pass_percentage'      => $request->pass_percentage,
            'negative_marking'     => $request->boolean('negative_marking'),
            'negative_value'       => $request->negative_value ?? 0.25,
            'is_active'            => $request->has('is_active'),
            'is_featured'          => $request->has('is_featured'),
            'created_by'           => auth()->id(),
        ]);

        return redirect()->route('admin.learning.mock-tests.index')
            ->with('success', 'Mock test created successfully.');
    }

    public function show(MockTest $mockTest)
    {
        $mockTest->load('learningCategory:id,name_en');
        $attemptsCount = TestAttempt::where('mock_test_id', $mockTest->id)->count();
        $passedCount   = TestAttempt::where('mock_test_id', $mockTest->id)->where('passed', true)->count();
        $avgScore      = TestAttempt::where('mock_test_id', $mockTest->id)->avg('score_percentage') ?? 0;

        $recentAttempts = TestAttempt::where('mock_test_id', $mockTest->id)
            ->with('user:id,name,email')
            ->latest()
            ->limit(20)
            ->get();

        // Wrong-answer frequency
        $wrongFrequency = [];
        foreach ($recentAttempts as $attempt) {
            foreach ($attempt->answers ?? [] as $ans) {
                $qId      = $ans['question_id'] ?? null;
                $selected = $ans['selected'] ?? -1;
                if (!$qId) continue;
                $q = QuizQuestion::withoutGlobalScopes()->find($qId);
                if ($q && (int)$selected !== -1 && (int)$selected !== (int)$q->correct_index) {
                    $wrongFrequency[$qId] = ($wrongFrequency[$qId] ?? 0) + 1;
                }
            }
        }
        arsort($wrongFrequency);
        $topWrongIds       = array_slice(array_keys($wrongFrequency), 0, 10, true);
        $topWrongQuestions = QuizQuestion::withoutGlobalScopes()
            ->whereIn('id', $topWrongIds)->get()->keyBy('id');

        // Percentile distribution buckets  (0-9, 10-19, ..., 90-100)
        $percentileDistribution = [];
        for ($i = 0; $i < 10; $i++) {
            $percentileDistribution[$i * 10] = 0;
        }
        TestAttempt::where('mock_test_id', $mockTest->id)
            ->whereNotNull('percentile_score')
            ->pluck('percentile_score')
            ->each(function ($p) use (&$percentileDistribution) {
                $bucket = min((int)floor($p / 10) * 10, 90);
                $percentileDistribution[$bucket]++;
            });

        // Avg percentile
        $avgPercentile = TestAttempt::where('mock_test_id', $mockTest->id)
            ->whereNotNull('percentile_score')
            ->avg('percentile_score') ?? 0;

        // Difficulty accuracy aggregated across all scored attempts
        $difficultyStats = ['easy' => [], 'medium' => [], 'hard' => []];
        TestAttempt::where('mock_test_id', $mockTest->id)
            ->whereNotNull('difficulty_breakdown')
            ->pluck('difficulty_breakdown')
            ->each(function ($bd) use (&$difficultyStats) {
                foreach (['easy', 'medium', 'hard'] as $d) {
                    if (isset($bd[$d]['accuracy']) && $bd[$d]['accuracy'] !== null) {
                        $difficultyStats[$d][] = $bd[$d]['accuracy'];
                    }
                }
            });
        $avgDifficultyAccuracy = [];
        foreach ($difficultyStats as $d => $vals) {
            $avgDifficultyAccuracy[$d] = count($vals) > 0 ? round(array_sum($vals) / count($vals)) : null;
        }

        return view('admin.learning.mock-tests.show', compact(
            'mockTest', 'attemptsCount', 'passedCount', 'avgScore',
            'recentAttempts', 'wrongFrequency', 'topWrongIds', 'topWrongQuestions',
            'percentileDistribution', 'avgPercentile', 'avgDifficultyAccuracy'
        ));
    }

    public function edit(MockTest $mockTest)
    {
        $categories = LearningCategory::active()->ordered()->get();
        return view('admin.learning.mock-tests.edit', compact('mockTest', 'categories'));
    }

    public function update(Request $request, MockTest $mockTest)
    {
        $request->validate([
            'learning_category_id' => 'required|exists:learning_categories,id',
            'title'                => 'required|string|max:255',
            'title_np'             => 'nullable|string|max:255',
            'description'          => 'nullable|string',
            'description_np'       => 'nullable|string',
            'question_count'       => 'required|integer|min:5|max:200',
            'duration_minutes'     => 'required|integer|min:5|max:300',
            'pass_percentage'      => 'required|integer|min:1|max:100',
            'negative_marking'     => 'nullable|boolean',
            'negative_value'       => 'nullable|numeric|min:0|max:1',
        ]);

        $category = LearningCategory::find($request->learning_category_id);

        $mockTest->update([
            'learning_category_id' => $request->learning_category_id,
            'title'                => $request->title,
            'title_np'             => $request->title_np,
            'description'          => $request->description,
            'description_np'       => $request->description_np,
            'category'             => $category->slug,
            'question_count'       => $request->question_count,
            'duration_minutes'     => $request->duration_minutes,
            'pass_percentage'      => $request->pass_percentage,
            'negative_marking'     => $request->boolean('negative_marking'),
            'negative_value'       => $request->negative_value ?? 0.25,
            'is_active'            => $request->has('is_active'),
            'is_featured'          => $request->has('is_featured'),
        ]);

        return redirect()->route('admin.learning.mock-tests.index')
            ->with('success', 'Mock test updated successfully.');
    }

    public function destroy(MockTest $mockTest)
    {
        $mockTest->delete();
        return back()->with('success', 'Mock test deleted.');
    }

    // ── Question Assignment ────────────────────────────────────────────────

    /** Show the question picker for a mock test */
    public function questions(MockTest $mockTest)
    {
        $mockTest->load('learningCategory:id,name_en,slug', 'fixedQuestions');

        // All active questions in the same category (for the picker)
        $available = QuizQuestion::active()
            ->where('category', $mockTest->category)
            ->orderBy('difficulty')
            ->orderBy('id')
            ->get();

        $assignedIds = $mockTest->fixedQuestions->pluck('id')->toArray();

        return view('admin.learning.mock-tests.questions', compact('mockTest', 'available', 'assignedIds'));
    }

    /** Save the assigned question set */
    public function syncQuestions(Request $request, MockTest $mockTest)
    {
        $request->validate([
            'question_ids'   => 'nullable|array',
            'question_ids.*' => 'integer|exists:quiz_questions,id',
            'use_fixed'      => 'nullable|boolean',
        ]);

        $useFixed = $request->boolean('use_fixed');
        $ids      = $request->input('question_ids', []);

        // Build sync payload with display_order
        $syncData = [];
        foreach (array_values($ids) as $order => $qid) {
            $syncData[$qid] = ['display_order' => $order + 1];
        }

        $mockTest->fixedQuestions()->sync($syncData);

        $mockTest->update([
            'use_fixed_questions' => $useFixed && count($ids) > 0,
            // Auto-update question_count to match fixed set if fixed mode enabled
            'question_count'      => ($useFixed && count($ids) > 0) ? count($ids) : $mockTest->question_count,
        ]);

        return redirect()->route('admin.learning.mock-tests.questions', $mockTest)
            ->with('success', $useFixed
                ? count($ids) . ' questions assigned. Test will use these specific questions.'
                : 'Question list saved. Test will use random pool (fixed mode off).'
            );
    }
}
