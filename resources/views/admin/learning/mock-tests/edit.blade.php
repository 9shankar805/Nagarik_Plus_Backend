@extends('admin.layouts.app')

@section('title', 'Edit Mock Test')
@section('subtitle', $mockTest->title)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.learning.mock-tests.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Mock Tests</a>
    <h2 class="text-2xl font-bold text-gray-900 mt-2">Edit: {{ Str::limit($mockTest->title, 60) }}</h2>
</div>

<form action="{{ route('admin.learning.mock-tests.update', $mockTest) }}" method="POST">
    @csrf @method('PUT')
    <div class="grid grid-cols-3 gap-6">

        {{-- Main --}}
        <div class="col-span-2 space-y-5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="font-semibold text-gray-800 border-b pb-2">Basic Information</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Title (English) <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $mockTest->title) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Title (Nepali)</label>
                        <input type="text" name="title_np" value="{{ old('title_np', $mockTest->title_np) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Description (English)</label>
                        <textarea name="description" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">{{ old('description', $mockTest->description) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Description (Nepali)</label>
                        <textarea name="description_np" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">{{ old('description_np', $mockTest->description_np) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="font-semibold text-gray-800 border-b pb-2">Scoring & Timing Rules</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">No. of Questions</label>
                        <input type="number" name="question_count" value="{{ old('question_count', $mockTest->question_count) }}" min="5" max="200"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Duration (minutes)</label>
                        <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $mockTest->duration_minutes) }}" min="5"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Pass Percentage</label>
                        <input type="number" name="pass_percentage" value="{{ old('pass_percentage', $mockTest->pass_percentage) }}" min="1" max="100"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="negative_marking" value="1" id="neg_marking"
                               {{ old('negative_marking', $mockTest->negative_marking) ? 'checked' : '' }}
                               class="w-4 h-4 text-red-600 rounded border-gray-300"
                               onchange="document.getElementById('neg_value_row').style.display = this.checked ? 'block' : 'none'">
                        <span class="text-sm font-semibold text-gray-700">Enable Negative Marking</span>
                    </label>
                    <div id="neg_value_row" style="display: {{ old('negative_marking', $mockTest->negative_marking) ? 'block' : 'none' }}">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Penalty per wrong answer</label>
                        <div class="flex items-center gap-2 max-w-xs">
                            <span class="text-gray-500 text-sm font-mono">−</span>
                            <input type="number" name="negative_value" value="{{ old('negative_value', $mockTest->negative_value) }}"
                                   step="0.25" min="0.25" max="1"
                                   class="w-32 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-span-1 space-y-5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
                <h3 class="font-semibold text-gray-800 border-b pb-2">Settings</h3>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                    <select name="learning_category_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('learning_category_id', $mockTest->learning_category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->icon }} {{ $cat->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $mockTest->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 text-[#4A5D4A] rounded border-gray-300">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1"
                               {{ old('is_featured', $mockTest->is_featured) ? 'checked' : '' }}
                               class="w-4 h-4 text-yellow-500 rounded border-gray-300">
                        <span class="text-sm font-medium text-gray-700">⭐ Featured</span>
                    </label>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <button type="submit"
                        class="w-full px-5 py-2.5 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] transition font-medium text-sm">
                    Update Mock Test
                </button>
                <a href="{{ route('admin.learning.mock-tests.show', $mockTest) }}"
                   class="w-full text-center px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                    View Stats
                </a>
            </div>
        </div>
    </div>
</form>
@endsection
