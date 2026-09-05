@extends('admin.layouts.app')
@section('title', 'Create Program')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.learning.programs.index') }}" class="text-sm text-blue-600 hover:text-blue-700">← Back</a>
    <h2 class="text-2xl font-bold text-gray-900 mt-2">Create Learning Program</h2>
    <p class="text-sm text-gray-500 mt-1">A Program is the top-level container (e.g. "Loksewa Preparation"). It contains Courses, which contain Subjects, which contain Chapters.</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl space-y-5">
    <form action="{{ route('admin.learning.programs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Title (English) *</label>
                <input type="text" name="title_en" value="{{ old('title_en') }}" required
                       placeholder="e.g. Loksewa Preparation"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] @error('title_en') border-red-400 @enderror">
                @error('title_en')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Title (Nepali)</label>
                <input type="text" name="title_np" value="{{ old('title_np') }}"
                       placeholder="e.g. लोकसेवा तयारी"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description (English)</label>
                <textarea name="description_en" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">{{ old('description_en') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description (Nepali)</label>
                <textarea name="description_np" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">{{ old('description_np') }}</textarea>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Category *</label>
                <select name="learning_category_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    <option value="">Select category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('learning_category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->icon }} {{ $cat->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Icon (emoji)</label>
                <input type="text" name="icon" value="{{ old('icon', '📚') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Color</label>
                <input type="color" name="color_code" value="{{ old('color_code', '#4A5D4A') }}"
                       class="h-10 w-full border border-gray-300 rounded-lg cursor-pointer">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Price (NPR, 0 = Free)</label>
                <input type="number" name="price" value="{{ old('price', 0) }}" min="0" step="0.01"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Display Order</label>
                <input type="number" name="display_order" value="{{ old('display_order', 0) }}" min="0"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Thumbnail</label>
                <input type="file" name="thumbnail_file" accept="image/*"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}
                   class="w-4 h-4 text-[#4A5D4A] rounded border-gray-300">
            <span class="text-sm font-medium text-gray-700">Publish immediately</span>
        </label>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] font-medium text-sm">Create Program</button>
            <a href="{{ route('admin.learning.programs.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection
