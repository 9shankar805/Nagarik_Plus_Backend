<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyQuiz;
use App\Models\DailyQuizEntry;
use App\Models\LearningCategory;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;

class AdminDailyQuizController extends Controller
{
    public function index(Request $request)
    {
        $query = DailyQuiz::with(['question:id,question,difficulty', 'category:id,name_en,slug'])
            ->orderByDesc('quiz_date');

        if ($request->category_id) {
            $query->where('learning_category_id', $request->category_id);
        }

        $quizzes    = $query->paginate(20)->withQueryString();
        $categories = LearningCategory::active()->ordered()->get();
        $today      = today()->toDateString();

        return view('admin.learning.daily-quiz.index', compact('quizzes', 'categories', 'today'));
    }

    public function create()
    {
        $categories = LearningCategory::active()->ordered()->get();
        $questions  = QuizQuestion::active()->select('id', 'question', 'category', 'difficulty')->get();
        $tomorrow   = today()->addDay()->toDateString();

        return view('admin.learning.daily-quiz.create', compact('categories', 'questions', 'tomorrow'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'quiz_question_id'     => 'required|exists:quiz_questions,id',
            'learning_category_id' => 'nullable|exists:learning_categories,id',
            'quiz_date'            => 'required|date',
        ]);

        // Prevent duplicate for same date + category
        $exists = DailyQuiz::where('quiz_date', $request->quiz_date)
            ->where('learning_category_id', $request->learning_category_id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['quiz_date' => 'A daily quiz already exists for this date and category.'])->withInput();
        }

        DailyQuiz::create([
            'quiz_question_id'     => $request->quiz_question_id,
            'learning_category_id' => $request->learning_category_id ?: null,
            'quiz_date'            => $request->quiz_date,
            'is_active'            => $request->has('is_active'),
        ]);

        return redirect()->route('admin.learning.daily-quiz.index')
            ->with('success', 'Daily quiz scheduled successfully.');
    }

    public function edit(DailyQuiz $dailyQuiz)
    {
        $categories = LearningCategory::active()->ordered()->get();
        $questions  = QuizQuestion::active()->select('id', 'question', 'category', 'difficulty')->get();

        return view('admin.learning.daily-quiz.edit', compact('dailyQuiz', 'categories', 'questions'));
    }

    public function update(Request $request, DailyQuiz $dailyQuiz)
    {
        $request->validate([
            'quiz_question_id'     => 'required|exists:quiz_questions,id',
            'learning_category_id' => 'nullable|exists:learning_categories,id',
            'quiz_date'            => 'required|date',
        ]);

        $dailyQuiz->update([
            'quiz_question_id'     => $request->quiz_question_id,
            'learning_category_id' => $request->learning_category_id ?: null,
            'quiz_date'            => $request->quiz_date,
            'is_active'            => $request->has('is_active'),
        ]);

        return redirect()->route('admin.learning.daily-quiz.index')
            ->with('success', 'Daily quiz updated.');
    }

    public function destroy(DailyQuiz $dailyQuiz)
    {
        $dailyQuiz->delete();
        return back()->with('success', 'Daily quiz deleted.');
    }

    /** Show participation stats for a quiz */
    public function stats(DailyQuiz $dailyQuiz)
    {
        $entries = DailyQuizEntry::where('daily_quiz_id', $dailyQuiz->id)
            ->with('user:id,name,email')
            ->latest('answered_at')
            ->paginate(30);

        $totalEntries   = DailyQuizEntry::where('daily_quiz_id', $dailyQuiz->id)->count();
        $correctEntries = DailyQuizEntry::where('daily_quiz_id', $dailyQuiz->id)->where('is_correct', true)->count();

        return view('admin.learning.daily-quiz.stats', compact(
            'dailyQuiz', 'entries', 'totalEntries', 'correctEntries'
        ));
    }
}
