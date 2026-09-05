@extends('admin.layouts.app')
@section('title', isset($course) ? 'Edit Course' : 'Add Course')
@section('subtitle', $program->title_en)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.learning.programs.show', $program) }}" class="text-sm text-blue-600 hover:text-blue-700">← Back to {{ $program->title_en }}</a>
    <h2 class="text-2xl font-bold text-gray-900 mt-2">{{ isset($course) ? 'Edit Course: '.$course->title_en : 'Add Course to '.$program->title_en }}</h2>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-xl space-y-5">
    @if(isset($course))
        <form action="{{ route('admin.learning.courses.update', $course) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
    @else
        <form action="{{ route('admin.learning.courses.store', $program) }}" method="POST" enctype="multipart/form-data">
            @csrf
    @endif

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Course Title (EN) *</label>
                <input type="text" name="title_en" value="{{ old('title_en', $course->title_en ?? '') }}" required
                       placeholder="e.g. Kharidar Level"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Course Title (NP)</label>
                <input type="text" name="title_np" value="{{ old('title_np', $course->title_np ?? '') }}"
                       placeholder="e.g. खरिदार तह"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
            <textarea name="description_en" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">{{ old('description_en', $course->description_en ?? '') }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Display Order</label>
                <input type="number" name="display_order" value="{{ old('display_order', $course->display_order ?? 0) }}" min="0"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Thumbnail</label>
                @if(isset($course) && $course->thumbnail_url)
                    <img src="{{ $course->thumbnail_url }}" class="h-10 w-10 rounded object-cover mb-1">
                @endif
                <input type="file" name="thumbnail_file" accept="image/*"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_published" value="1"
                   {{ old('is_published', $course->is_published ?? false) ? 'checked' : '' }}
                   class="w-4 h-4 text-[#4A5D4A] rounded border-gray-300">
            <span class="text-sm font-medium text-gray-700">Published</span>
        </label>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] font-medium text-sm">
                {{ isset($course) ? 'Update Course' : 'Add Course' }}
            </button>
            <a href="{{ route('admin.learning.programs.show', $program) }}"
               class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection
