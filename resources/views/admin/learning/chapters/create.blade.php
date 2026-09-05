@extends('admin.layouts.app')

@section('title', 'Add Chapter')
@section('subtitle', 'Create new study material')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.learning.chapters.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Chapters</a>
    <h2 class="text-2xl font-bold text-gray-900 mt-2">Add Study Chapter</h2>
</div>

<form action="{{ route('admin.learning.chapters.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-3 gap-6">

        {{-- Main Content --}}
        <div class="col-span-2 space-y-5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="font-semibold text-gray-800 border-b pb-2">Content</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Title (English) <span class="text-red-500">*</span></label>
                        <input type="text" name="title_en" value="{{ old('title_en') }}" required
                               placeholder="e.g. Traffic Signs & Rules"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] @error('title_en') border-red-400 @enderror">
                        @error('title_en')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Title (Nepali)</label>
                        <input type="text" name="title_np" value="{{ old('title_np') }}"
                               placeholder="e.g. ट्राफिक चिह्न र नियम"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Summary (English)</label>
                        <textarea name="summary_en" rows="3" placeholder="Brief summary shown in chapter list..."
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">{{ old('summary_en') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Summary (Nepali)</label>
                        <textarea name="summary_np" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">{{ old('summary_np') }}</textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Full Content (English)</label>
                    <textarea name="content_en" id="content_en" rows="12"
                              placeholder="Write the full chapter content here. HTML is supported."
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] font-mono">{{ old('content_en') }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">HTML supported: &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;strong&gt;, &lt;img&gt;, etc.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Full Content (Nepali)</label>
                    <textarea name="content_np" rows="12"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] font-mono">{{ old('content_np') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Sidebar Settings --}}
        <div class="col-span-1 space-y-5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
                <h3 class="font-semibold text-gray-800 border-b pb-2">Settings</h3>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Content Type <span class="text-red-500">*</span></label>
                    <select name="content_type" required id="content_type_select"
                            onchange="toggleDuration(this.value)"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}" {{ old('content_type', 'note') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">This determines which count column it increments (video, note, model set, etc.)</p>
                </div>

                <div id="duration_field" style="display:none">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes') }}" min="1" max="600"
                           placeholder="e.g. 45"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    <p class="text-xs text-gray-400 mt-1">Length of video/lecture. Used to compute total video hours on course page.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                    <select name="learning_category_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] @error('learning_category_id') border-red-400 @enderror">
                        <option value="">Select category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('learning_category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->icon }} {{ $cat->name_en }}
                            </option>
                        @endforeach
                    </select>
                    @error('learning_category_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Subject</label>
                    <select name="subject_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                        <option value="">— No Subject —</option>
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}" {{ old('subject_id') == $sub->id ? 'selected' : '' }}>
                                {{ $sub->title_en }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Assigning a subject auto-updates its lecture/video/note counts.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Display Order</label>
                    <input type="number" name="display_order" value="{{ old('display_order', 0) }}" min="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Est. Read Time (minutes)</label>
                    <input type="number" name="read_time_minutes" value="{{ old('read_time_minutes', 5) }}" min="1" max="120"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Video URL</label>
                    <input type="url" name="video_url" value="{{ old('video_url') }}"
                           placeholder="https://youtube.com/..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Cover Image</label>
                    <input type="file" name="image_file" accept="image/jpeg,image/png,image/jpg,image/webp"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <p class="text-xs text-gray-400 mt-1">Max 5MB. Recommended: 800×450px.</p>
                </div>

                <div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}
                               class="w-4 h-4 text-[#4A5D4A] rounded border-gray-300">
                        <span class="text-sm font-medium text-gray-700">Publish immediately</span>
                    </label>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <button type="submit"
                        class="w-full px-5 py-2.5 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] transition font-medium text-sm">
                    Save Chapter
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
// Init on load
toggleDuration(document.getElementById('content_type_select').value);
</script>
@endsection
