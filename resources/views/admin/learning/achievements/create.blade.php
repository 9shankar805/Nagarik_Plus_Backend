@extends('admin.layouts.app')
@section('title', 'Create Achievement')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.learning.achievements.index') }}" class="text-sm text-blue-600 hover:text-blue-700">← Back</a>
    <h2 class="text-2xl font-bold text-gray-900 mt-2">Create Achievement Badge</h2>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl space-y-5">
    <form action="{{ route('admin.learning.achievements.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Slug (unique key) *</label>
                <input type="text" name="slug" value="{{ old('slug') }}" required placeholder="e.g. streak_7_days"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-[#4A5D4A] @error('slug') border-red-400 @enderror">
                @error('slug')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Icon (emoji)</label>
                <input type="text" name="icon" value="{{ old('icon', '🏅') }}" placeholder="🔥"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Title (English) *</label>
                <input type="text" name="title_en" value="{{ old('title_en') }}" required placeholder="7-Day Streak!"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Title (Nepali)</label>
                <input type="text" name="title_np" value="{{ old('title_np') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description (EN)</label>
                <textarea name="description_en" rows="2" placeholder="Study for 7 consecutive days."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">{{ old('description_en') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description (NP)</label>
                <textarea name="description_np" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">{{ old('description_np') }}</textarea>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Type *</label>
                <select name="type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}" {{ old('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Threshold *</label>
                <input type="number" name="threshold" value="{{ old('threshold', 1) }}" min="1" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                <p class="text-xs text-gray-400 mt-1">e.g. 7 for "7-day streak"</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Badge Color</label>
                <div class="flex gap-2">
                    <input type="color" name="badge_color" value="{{ old('badge_color', '#4A5D4A') }}"
                           class="h-10 w-14 border border-gray-300 rounded-lg cursor-pointer">
                    <input type="text" id="badge_color_text" value="{{ old('badge_color', '#4A5D4A') }}"
                           class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-[#4A5D4A]" readonly>
                </div>
            </div>
        </div>

        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                   class="w-4 h-4 text-[#4A5D4A] rounded border-gray-300">
            <span class="text-sm font-medium text-gray-700">Active (users can earn this badge)</span>
        </label>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] font-medium text-sm">
                Create Badge
            </button>
            <a href="{{ route('admin.learning.achievements.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
