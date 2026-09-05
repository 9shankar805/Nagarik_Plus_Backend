<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminQuizController extends Controller
{
    public function index()
    {
        $questions = QuizQuestion::latest()->paginate(25);
        return view('admin.quiz.index', compact('questions'));
    }

    public function create()
    {
        return view('admin.quiz.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question'         => 'required|string',
            'question_np'      => 'nullable|string',
            'category'         => 'required|string|max:100',
            'topic_id'         => 'nullable|string|max:100',
            'difficulty'       => 'required|in:easy,medium,hard',
            'difficulty_weight'=> 'nullable|numeric|min:0.1|max:5',
            'option_0'         => 'required|string',
            'option_1'         => 'required|string',
            'option_2'         => 'required|string',
            'option_3'         => 'required|string',
            'correct_index'    => 'required|integer|min:0|max:3',
            'explanation'      => 'nullable|string',
            'explanation_np'   => 'nullable|string',
            'image_file'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5000',
        ]);

        $options = [
            $request->option_0,
            $request->option_1,
            $request->option_2,
            $request->option_3,
        ];

        $optionsNp = null;
        if ($request->filled('option_np_0')) {
            $optionsNp = [
                $request->option_np_0 ?? '',
                $request->option_np_1 ?? '',
                $request->option_np_2 ?? '',
                $request->option_np_3 ?? '',
            ];
        }

        $imageUrl = $request->image_url;
        if ($request->hasFile('image_file')) {
            $path     = $request->file('image_file')->store('quiz/images', 'public');
            $imageUrl = Storage::url($path);
        }

        // Auto-set weight from difficulty if not explicitly provided
        $weight = $request->filled('difficulty_weight')
            ? $request->difficulty_weight
            : match($request->difficulty) {
                'easy' => 0.50,
                'hard' => 2.00,
                default => 1.00,
            };

        QuizQuestion::create([
            'question'         => $request->question,
            'question_np'      => $request->question_np,
            'category'         => $request->category,
            'topic_id'         => $request->topic_id ?: null,
            'difficulty'       => $request->difficulty,
            'difficulty_weight'=> $weight,
            'options'          => $options,
            'options_np'       => $optionsNp,
            'correct_index'    => (int) $request->correct_index,
            'explanation'      => $request->explanation,
            'explanation_np'   => $request->explanation_np,
            'image_url'        => $imageUrl,
            'is_active'        => $request->has('is_active'),
        ]);

        return redirect()->route('admin.quiz.index')->with('success', 'Quiz question created successfully.');
    }

    public function edit(QuizQuestion $quiz)
    {
        return view('admin.quiz.edit', compact('quiz'));
    }

    public function update(Request $request, QuizQuestion $quiz)
    {
        $request->validate([
            'question'         => 'required|string',
            'question_np'      => 'nullable|string',
            'category'         => 'required|string|max:100',
            'topic_id'         => 'nullable|string|max:100',
            'difficulty'       => 'required|in:easy,medium,hard',
            'difficulty_weight'=> 'nullable|numeric|min:0.1|max:5',
            'option_0'         => 'required|string',
            'option_1'         => 'required|string',
            'option_2'         => 'required|string',
            'option_3'         => 'required|string',
            'correct_index'    => 'required|integer|min:0|max:3',
            'explanation'      => 'nullable|string',
            'explanation_np'   => 'nullable|string',
            'image_file'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5000',
        ]);

        $options = [
            $request->option_0,
            $request->option_1,
            $request->option_2,
            $request->option_3,
        ];

        $optionsNp = null;
        if ($request->filled('option_np_0')) {
            $optionsNp = [
                $request->option_np_0 ?? '',
                $request->option_np_1 ?? '',
                $request->option_np_2 ?? '',
                $request->option_np_3 ?? '',
            ];
        }

        $imageUrl = $quiz->image_url;
        if ($request->hasFile('image_file')) {
            $path     = $request->file('image_file')->store('quiz/images', 'public');
            $imageUrl = Storage::url($path);
        }

        $weight = $request->filled('difficulty_weight')
            ? $request->difficulty_weight
            : match($request->difficulty) {
                'easy' => 0.50,
                'hard' => 2.00,
                default => 1.00,
            };

        $quiz->update([
            'question'         => $request->question,
            'question_np'      => $request->question_np,
            'category'         => $request->category,
            'topic_id'         => $request->topic_id ?: null,
            'difficulty'       => $request->difficulty,
            'difficulty_weight'=> $weight,
            'options'          => $options,
            'options_np'       => $optionsNp,
            'correct_index'    => (int) $request->correct_index,
            'explanation'      => $request->explanation,
            'explanation_np'   => $request->explanation_np,
            'image_url'        => $imageUrl,
            'is_active'        => $request->has('is_active'),
        ]);

        return redirect()->route('admin.quiz.index')->with('success', 'Quiz question updated successfully.');
    }

    public function destroy(QuizQuestion $quiz)
    {
        $quiz->delete();
        return back()->with('success', 'Tutorial Quiz question deleted.');
    }
}
