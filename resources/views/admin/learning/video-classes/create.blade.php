@extends('admin.layouts.app')

@section('title', 'Add Video Class')
@section('subtitle', 'Upload a recorded or schedule a live video lesson')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.learning.video-classes.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Video Classes</a>
    <h2 class="text-2xl font-bold text-gray-900 mt-2">Add Video Class</h2>
</div>

<form action="{{ route('admin.learning.video-classes.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-3 gap-6">

        {{-- Main --}}
        <div class="col-span-2 space-y-5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="font-semibold text-gray-800 border-b pb-2">Video Details</h3>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           placeholder="e.g. Introduction to Loksewa Constitution"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] @error('title') border-red-400 @enderror">
                    @error('title')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" placeholder="What will students learn in this video?"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Video URL <span class="text-red-500">*</span></label>
                    <input type="url" name="video_url" value="{{ old('video_url') }}" required
                           placeholder="https://youtube.com/watch?v=... or https://vimeo.com/..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] @error('video_url') border-red-400 @enderror">
                    @error('video_url')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    <p class="text-xs text-gray-400 mt-1">YouTube, Vimeo, or direct MP4 link. For live streams, paste the stream URL.</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Duration (minutes)</label>
                        <input type="number" name="duration_minutes" value="{{ old('duration_minutes') }}" min="1" max="600"
                               placeholder="e.g. 45"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Live Scheduled At</label>
                        <input type="datetime-local" name="live_scheduled_at" value="{{ old('live_scheduled_at') }}"
                               id="live_scheduled_at"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Thumbnail Image</label>
                    <input type="file" name="thumbnail_file" accept="image/jpeg,image/png,image/webp"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG or WebP. Max 5MB. Recommended: 16:9 ratio.</p>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-span-1 space-y-5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
                <h3 class="font-semibold text-gray-800 border-b pb-2">Settings</h3>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                    <select name="learning_category_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                        <option value="">— No Category —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('learning_category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->icon }} {{ $cat->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                    <select name="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                        <option value="active"   {{ old('status','active') == 'active'   ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_live" value="1" id="is_live_cb"
                           {{ old('is_live') ? 'checked' : '' }}
                           class="w-4 h-4 text-red-600 rounded border-gray-300"
                           onchange="document.getElementById('live_scheduled_at').closest('.grid').style.opacity = this.checked ? '1' : '0.5'">
                    <span class="text-sm font-medium text-gray-700">🔴 This is a Live Class</span>
                </label>
                <p class="text-xs text-gray-400">Check this to mark as a live stream and show the scheduled time to users.</p>
            </div>

            <div class="flex flex-col gap-3">
                <button type="submit"
                        class="w-full px-5 py-2.5 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] transition font-medium text-sm">
                    Create Video Class
                </button>
                <a href="{{ route('admin.learning.video-classes.index') }}"
                   class="w-full text-center px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</form>
@endsection
