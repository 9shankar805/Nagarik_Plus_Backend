@extends('admin.layouts.app')
@section('title', 'Edit Program')
@section('subtitle', $program->title_en)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.learning.programs.show', $program) }}" class="text-sm text-blue-600 hover:text-blue-700">← Back to Program</a>
    <h2 class="text-2xl font-bold text-gray-900 mt-2">Edit: {{ $program->title_en }}</h2>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl space-y-5">
    <form action="{{ route('admin.learning.programs.update', $program) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Title (English) *</label>
                <input type="text" name="title_en" value="{{ old('title_en', $program->title_en) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Title (Nepali)</label>
                <input type="text" name="title_np" value="{{ old('title_np', $program->title_np) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description (EN)</label>
                <textarea name="description_en" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">{{ old('description_en', $program->description_en) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description (NP)</label>
                <textarea name="description_np" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">{{ old('description_np', $program->description_np) }}</textarea>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Category *</label>
                <select name="learning_category_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('learning_category_id', $program->learning_category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->icon }} {{ $cat->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Icon</label>
                <input type="text" name="icon" value="{{ old('icon', $program->icon) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Color</label>
                <input type="color" name="color_code" value="{{ old('color_code', $program->color_code ?? '#4A5D4A') }}"
                       class="h-10 w-full border border-gray-300 rounded-lg cursor-pointer">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Price (NPR)</label>
                <input type="number" name="price" value="{{ old('price', $program->price) }}" min="0" step="0.01"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Display Order</label>
                <input type="number" name="display_order" value="{{ old('display_order', $program->display_order) }}" min="0"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Thumbnail</label>
                @if($program->thumbnail_url)
                    <img src="{{ $program->thumbnail_url }}" class="h-10 w-10 rounded object-cover mb-1">
                @endif
                <input type="file" name="thumbnail_file" accept="image/*"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_published" value="1" {{ old('is_published', $program->is_published) ? 'checked' : '' }}
                   class="w-4 h-4 text-[#4A5D4A] rounded border-gray-300">
            <span class="text-sm font-medium text-gray-700">Published</span>
        </label>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] font-medium text-sm">Update Program</button>
            <a href="{{ route('admin.learning.programs.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection
