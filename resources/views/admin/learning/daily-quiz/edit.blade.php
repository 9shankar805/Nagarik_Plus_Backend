@extends('admin.layouts.app')
@section('title', 'Edit Daily Quiz')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.learning.daily-quiz.index') }}" class="text-sm text-blue-600 hover:text-blue-700">← Back</a>
    <h2 class="text-2xl font-bold text-gray-900 mt-2">Edit Daily Quiz — {{ $dailyQuiz->quiz_date->format('d M Y') }}</h2>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl space-y-5">
    <form action="{{ route('admin.learning.daily-quiz.update', $dailyQuiz) }}" method="POST">
        @csrf @method('PUT')

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Quiz Date *</label>
                <input type="date" name="quiz_date" value="{{ old('quiz_date', $dailyQuiz->quiz_date->toDateString()) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                <select name="learning_category_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    <option value="">Global</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('learning_category_id', $dailyQuiz->learning_category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->icon }} {{ $cat->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Question *</label>
            <select name="quiz_question_id" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                @foreach($questions->groupBy('category') as $cat => $qs)
                    <optgroup label="{{ ucwords(str_replace('_', ' ', $cat)) }}">
                        @foreach($qs as $q)
                            <option value="{{ $q->id }}" {{ old('quiz_question_id', $dailyQuiz->quiz_question_id) == $q->id ? 'selected' : '' }}>
                                [{{ ucfirst($q->difficulty) }}] {{ Str::limit($q->question, 80) }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>

        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $dailyQuiz->is_active) ? 'checked' : '' }}
                   class="w-4 h-4 text-[#4A5D4A] rounded border-gray-300">
            <span class="text-sm font-medium text-gray-700">Active</span>
        </label>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] font-medium text-sm">Update</button>
            <a href="{{ route('admin.learning.daily-quiz.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection
