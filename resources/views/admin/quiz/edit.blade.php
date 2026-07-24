@extends('admin.layouts.app')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.quiz.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Quiz Questions</a>
    <h1 class="text-2xl font-bold text-gray-900 mt-2">Edit Tutorial Quiz Question #{{ $quiz->id }}</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-3xl">
    <form action="{{ route('admin.quiz.update', $quiz) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Question (English) <span class="text-red-500">*</span></label>
            <textarea name="question" required rows="2"
                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ $quiz->question }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Question (Nepali)</label>
            <textarea name="question_np" rows="2"
                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ $quiz->question_np }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                <select name="category" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="driving_license" @if($quiz->category === 'driving_license') selected @endif>Driving License Preparation</option>
                    <option value="traffic_rules" @if($quiz->category === 'traffic_rules') selected @endif>Traffic Rules & Signals</option>
                    <option value="lok_sewa" @if($quiz->category === 'lok_sewa') selected @endif>Lok Sewa General Knowledge</option>
                    <option value="citizen_duties" @if($quiz->category === 'citizen_duties') selected @endif>Citizen Rights & Duties</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Difficulty Level</label>
                <select name="difficulty" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="easy" @if($quiz->difficulty === 'easy') selected @endif>Easy</option>
                    <option value="medium" @if($quiz->difficulty === 'medium') selected @endif>Medium</option>
                    <option value="hard" @if($quiz->difficulty === 'hard') selected @endif>Hard</option>
                </select>
            </div>
        </div>

        @php
            $opts = $quiz->options ?? ['', '', '', ''];
        @endphp

        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-4">
            <h3 class="text-sm font-bold text-gray-800">4 Multiple Choice Options</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Option A (Index 0)</label>
                    <input type="text" name="option_0" value="{{ $opts[0] ?? '' }}" required
                           class="w-full border-gray-300 rounded-lg shadow-sm text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Option B (Index 1)</label>
                    <input type="text" name="option_1" value="{{ $opts[1] ?? '' }}" required
                           class="w-full border-gray-300 rounded-lg shadow-sm text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Option C (Index 2)</label>
                    <input type="text" name="option_2" value="{{ $opts[2] ?? '' }}" required
                           class="w-full border-gray-300 rounded-lg shadow-sm text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Option D (Index 3)</label>
                    <input type="text" name="option_3" value="{{ $opts[3] ?? '' }}" required
                           class="w-full border-gray-300 rounded-lg shadow-sm text-xs">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-blue-700 mb-1">Correct Answer Selection</label>
                <select name="correct_index" required class="w-full border-blue-300 bg-blue-50 text-blue-900 rounded-lg shadow-sm text-sm font-semibold">
                    <option value="0" @if($quiz->correct_index === 0) selected @endif>Option A (Index 0) is Correct</option>
                    <option value="1" @if($quiz->correct_index === 1) selected @endif>Option B (Index 1) is Correct</option>
                    <option value="2" @if($quiz->correct_index === 2) selected @endif>Option C (Index 2) is Correct</option>
                    <option value="3" @if($quiz->correct_index === 3) selected @endif>Option D (Index 3) is Correct</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Replace Question Image / Diagram</label>
            <input type="file" name="image_file" accept="image/*"
                   class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Answer Explanation (English)</label>
            <textarea name="explanation" rows="2"
                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ $quiz->explanation }}</textarea>
        </div>

        <div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" @if($quiz->is_active) checked @endif class="w-4 h-4 text-blue-600 rounded">
                <span class="text-sm font-medium text-gray-700">Active in Mobile Practice Tests</span>
            </label>
        </div>

        <div class="pt-4 flex gap-3">
            <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                Update Quiz Question
            </button>
            <a href="{{ route('admin.quiz.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
