@extends('admin.layouts.app')

@section('title', 'Edit Chapter')
@section('subtitle', $chapter->title_en)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.learning.chapters.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Chapters</a>
    <h2 class="text-2xl font-bold text-gray-900 mt-2">Edit: {{ Str::limit($chapter->title_en, 60) }}</h2>
</div>

<form action="{{ route('admin.learning.chapters.update', $chapter) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="grid grid-cols-3 gap-6">

        {{-- Main Content --}}
        <div class="col-span-2 space-y-5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="font-semibold text-gray-800 border-b pb-2">Content</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Title (English) <span class="text-red-500">*</span></label>
                        <input type="text" name="title_en" value="{{ old('title_en', $chapter->title_en) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] @error('title_en') border-red-400 @enderror">
                        @error('title_en')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Title (Nepali)</label>
                        <input type="text" name="title_np" value="{{ old('title_np', $chapter->title_np) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Summary (English)</label>
                        <textarea name="summary_en" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">{{ old('summary_en', $chapter->summary_en) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Summary (Nepali)</label>
                        <textarea name="summary_np" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">{{ old('summary_np', $chapter->summary_np) }}</textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Full Content (English)</label>
                    <textarea name="content_en" rows="12"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] font-mono">{{ old('content_en', $chapter->content_en) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Full Content (Nepali)</label>
                    <textarea name="content_np" rows="12"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] font-mono">{{ old('content_np', $chapter->content_np) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-span-1 space-y-5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
                <h3 class="font-semibold text-gray-800 border-b pb-2">Settings</h3>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Content Type <span class="text-red-500">*</span></label>
                    <select name="content_type" required id="content_type_select"
                            onchange="toggleDuration(this.value)"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}" {{ old('content_type', $chapter->content_type) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="duration_field" style="display:none">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Duration (minutes)</label>
                    <input type="number" name="duration_minutes"
                           value="{{ old('duration_minutes', $chapter->duration_minutes) }}" min="1" max="600"
                           placeholder="e.g. 45"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                    <select name="learning_category_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('learning_category_id', $chapter->learning_category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->icon }} {{ $cat->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Subject</label>
                    <select name="subject_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                        <option value="">— No Subject —</option>
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}"
                                {{ old('subject_id', $chapter->subject_id) == $sub->id ? 'selected' : '' }}>
                                {{ $sub->title_en }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Saves to subject's lecture/video/note count automatically.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Display Order</label>
                    <input type="number" name="display_order" value="{{ old('display_order', $chapter->display_order) }}" min="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Est. Read Time (minutes)</label>
                    <input type="number" name="read_time_minutes" value="{{ old('read_time_minutes', $chapter->read_time_minutes) }}" min="1"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Video URL</label>
                    <input type="url" name="video_url" value="{{ old('video_url', $chapter->video_url) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Cover Image</label>
                    @if($chapter->image_url)
                        <img src="{{ $chapter->image_url }}" alt="Cover" class="h-20 w-full object-cover rounded-lg mb-2 border">
                    @endif
                    <input type="file" name="image_file" accept="image/jpeg,image/png,image/jpg,image/webp"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <p class="text-xs text-gray-400 mt-1">Leave empty to keep current image.</p>
                </div>

                <div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_published" value="1"
                               {{ old('is_published', $chapter->is_published) ? 'checked' : '' }}
                               class="w-4 h-4 text-[#4A5D4A] rounded border-gray-300">
                        <span class="text-sm font-medium text-gray-700">Published</span>
                    </label>
                    @if($chapter->published_at)
                        <p class="text-xs text-gray-400 mt-1">First published: {{ $chapter->published_at->format('d M Y') }}</p>
                    @endif
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <button type="submit"
                        class="w-full px-5 py-2.5 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] transition font-medium text-sm">
                    Update Chapter
                </button>
                <a href="{{ route('admin.learning.chapters.index') }}"
                   class="w-full text-center px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</form>

<script>
function toggleDuration(type) {
    const show = ['lecture', 'video', 'audio'].includes(type);
    document.getElementById('duration_field').style.display = show ? 'block' : 'none';
}
toggleDuration(document.getElementById('content_type_select').value);
</script>
@endsection
