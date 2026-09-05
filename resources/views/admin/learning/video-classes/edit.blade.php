@extends('admin.layouts.app')

@section('title', 'Edit Video Class')
@section('subtitle', 'Update video lesson details')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.learning.video-classes.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Video Classes</a>
    <h2 class="text-2xl font-bold text-gray-900 mt-2">Edit: {{ $videoClass->title }}</h2>
</div>

<form action="{{ route('admin.learning.video-classes.update', $videoClass) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="grid grid-cols-3 gap-6">

        {{-- Main --}}
        <div class="col-span-2 space-y-5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="font-semibold text-gray-800 border-b pb-2">Video Details</h3>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $videoClass->title) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] @error('title') border-red-400 @enderror">
                    @error('title')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">{{ old('description', $videoClass->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Video URL <span class="text-red-500">*</span></label>
                    <input type="url" name="video_url" value="{{ old('video_url', $videoClass->video_url) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] @error('video_url') border-red-400 @enderror">
                    @error('video_url')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Duration (minutes)</label>
                        <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $videoClass->duration_minutes) }}" min="1" max="600"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Live Scheduled At</label>
                        <input type="datetime-local" name="live_scheduled_at"
                               value="{{ old('live_scheduled_at', $videoClass->live_scheduled_at?->format('Y-m-d\TH:i')) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Thumbnail Image</label>
                    @if($videoClass->thumbnail_url)
                        <img src="{{ $videoClass->thumbnail_url }}" alt="Current thumbnail" class="w-40 h-24 object-cover rounded-lg border border-gray-200 mb-2">
                    @endif
                    <input type="file" name="thumbnail_file" accept="image/jpeg,image/png,image/webp"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    <p class="text-xs text-gray-400 mt-1">Leave blank to keep existing thumbnail.</p>
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
                            <option value="{{ $cat->id }}" {{ old('learning_category_id', $videoClass->learning_category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->icon }} {{ $cat->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                    <select name="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                        <option value="active"   {{ old('status', $videoClass->status) == 'active'   ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $videoClass->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_live" value="1"
                           {{ old('is_live', $videoClass->is_live) ? 'checked' : '' }}
                           class="w-4 h-4 text-red-600 rounded border-gray-300">
                    <span class="text-sm font-medium text-gray-700">🔴 This is a Live Class</span>
                </label>
            </div>

            <div class="flex flex-col gap-3">
                <button type="submit"
                        class="w-full px-5 py-2.5 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] transition font-medium text-sm">
                    Save Changes
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
