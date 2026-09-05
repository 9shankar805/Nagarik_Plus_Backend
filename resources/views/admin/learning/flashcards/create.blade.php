@extends('admin.layouts.app')
@section('title', 'Create Flashcard Set')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.learning.flashcards.index') }}" class="text-sm text-blue-600 hover:text-blue-700">← Back to Sets</a>
    <h2 class="text-2xl font-bold text-gray-900 mt-2">New Flashcard Set</h2>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form action="{{ route('admin.learning.flashcards.store') }}" method="POST" class="space-y-5">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Title (English) <span class="text-red-500">*</span></label>
                <input type="text" name="title_en" value="{{ old('title_en') }}" required
                       placeholder="e.g. Traffic Sign Vocabulary"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] @error('title_en') border-red-400 @enderror">
                @error('title_en')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Title (Nepali)</label>
                <input type="text" name="title_np" value="{{ old('title_np') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="2"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                <select name="learning_category_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    <option value="">— None —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('learning_category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->icon }} {{ $cat->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Linked Chapter (optional)</label>
                <select name="learning_chapter_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    <option value="">— None —</option>
                    @foreach($chapters as $ch)
                        <option value="{{ $ch->id }}" {{ old('learning_chapter_id') == $ch->id ? 'selected' : '' }}>
                            {{ $ch->title_en }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Display Order</label>
            <input type="number" name="display_order" value="{{ old('display_order', 0) }}" min="0"
                   class="w-32 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
        </div>

        <div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}
                       class="w-4 h-4 text-[#4A5D4A] rounded border-gray-300">
                <span class="text-sm font-medium text-gray-700">Publish immediately</span>
            </label>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] font-medium text-sm">
                Create Set
            </button>
            <a href="{{ route('admin.learning.flashcards.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
