@extends('admin.layouts.app')

@section('title', 'Edit Learning Category')
@section('subtitle', $category->name_en)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.learning.categories.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Categories</a>
    <h2 class="text-2xl font-bold text-gray-900 mt-2">Edit: {{ $category->name_en }}</h2>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form action="{{ route('admin.learning.categories.update', $category) }}" method="POST"
          enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Name (English) <span class="text-red-500">*</span></label>
                <input type="text" name="name_en" value="{{ old('name_en', $category->name_en) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] focus:border-transparent @error('name_en') border-red-400 @enderror">
                @error('name_en')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Name (Nepali)</label>
                <input type="text" name="name_np" value="{{ old('name_np', $category->name_np) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] focus:border-transparent">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description (English)</label>
                <textarea name="description_en" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] focus:border-transparent">{{ old('description_en', $category->description_en) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description (Nepali)</label>
                <textarea name="description_np" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] focus:border-transparent">{{ old('description_np', $category->description_np) }}</textarea>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Icon</label>
                <input type="text" name="icon" value="{{ old('icon', $category->icon) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Color Code</label>
                <input type="text" name="color_code" value="{{ old('color_code', $category->color_code) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Display Order</label>
                <input type="number" name="display_order" value="{{ old('display_order', $category->display_order) }}" min="0"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] focus:border-transparent">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Banner Image</label>
            @if($category->banner_url)
                <img src="{{ $category->banner_url }}" alt="Current banner" class="h-24 rounded-lg object-cover mb-2 border border-gray-200">
            @endif
            <input type="file" name="banner_file" accept="image/jpeg,image/png,image/jpg,image/webp"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <p class="text-xs text-gray-400 mt-1">Leave empty to keep current image.</p>
        </div>

        <div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 text-[#4A5D4A] rounded border-gray-300">
                <span class="text-sm font-medium text-gray-700">Active (visible to users)</span>
            </label>
        </div>

        <div class="pt-2 flex gap-3">
            <button type="submit"
                    class="px-6 py-2.5 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] transition font-medium text-sm">
                Update Category
            </button>
            <a href="{{ route('admin.learning.categories.index') }}"
               class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
