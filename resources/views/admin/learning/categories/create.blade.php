@extends('admin.layouts.app')

@section('title', 'Add Learning Category')
@section('subtitle', 'Create a new Learning Center section')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.learning.categories.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Categories</a>
    <h2 class="text-2xl font-bold text-gray-900 mt-2">Add Learning Category</h2>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form action="{{ route('admin.learning.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Name (English) <span class="text-red-500">*</span></label>
                <input type="text" name="name_en" value="{{ old('name_en') }}" required
                       placeholder="e.g. Driving License"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] focus:border-transparent @error('name_en') border-red-400 @enderror">
                @error('name_en')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Name (Nepali)</label>
                <input type="text" name="name_np" value="{{ old('name_np') }}"
                       placeholder="e.g. ड्राइभिङ लाइसेन्स"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] focus:border-transparent">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description (English)</label>
                <textarea name="description_en" rows="3" placeholder="Brief description of this category..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] focus:border-transparent">{{ old('description_en') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description (Nepali)</label>
                <textarea name="description_np" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] focus:border-transparent">{{ old('description_np') }}</textarea>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Icon (emoji or class)</label>
                <input type="text" name="icon" value="{{ old('icon', '📚') }}" placeholder="🚗"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] focus:border-transparent">
                <p class="text-xs text-gray-400 mt-1">Use an emoji or icon CSS class</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Color Code</label>
                <input type="text" name="color_code" value="{{ old('color_code', '#4A5D4A') }}" placeholder="#4A5D4A"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Display Order</label>
                <input type="number" name="display_order" value="{{ old('display_order', 0) }}" min="0"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] focus:border-transparent">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Banner Image</label>
            <input type="file" name="banner_file" accept="image/jpeg,image/png,image/jpg,image/webp"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <p class="text-xs text-gray-400 mt-1">Optional. Max 5MB. Recommended: 1200×400px.</p>
        </div>

        <div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                       class="w-4 h-4 text-[#4A5D4A] rounded border-gray-300">
                <span class="text-sm font-medium text-gray-700">Active (visible to users)</span>
            </label>
        </div>

        <div class="pt-2 flex gap-3">
            <button type="submit"
                    class="px-6 py-2.5 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] transition font-medium text-sm">
                Create Category
            </button>
            <a href="{{ route('admin.learning.categories.index') }}"
               class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
