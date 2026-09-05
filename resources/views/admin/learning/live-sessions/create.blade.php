@extends('admin.layouts.app')
@section('title', 'Schedule Live Session')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.learning.live-sessions.index') }}" class="text-sm text-blue-600 hover:text-blue-700">← Back</a>
    <h2 class="text-2xl font-bold text-gray-900 mt-2">Schedule Live Session</h2>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl space-y-5">
    <form action="{{ route('admin.learning.live-sessions.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Title (EN) *</label>
                <input type="text" name="title_en" value="{{ old('title_en') }}" required
                       placeholder="e.g. Constitution Live Class"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Title (NP)</label>
                <input type="text" name="title_np" value="{{ old('title_np') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="2"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Type *</label>
                <select name="type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    <option value="live" {{ old('type') === 'live' ? 'selected' : '' }}>🔴 Live Class</option>
                    <option value="recorded" {{ old('type') === 'recorded' ? 'selected' : '' }}>📹 Recorded</option>
                    <option value="doubt_clearing" {{ old('type') === 'doubt_clearing' ? 'selected' : '' }}>❓ Doubt Clearing</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                <select name="learning_category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    <option value="">Global (all users)</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('learning_category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->icon }} {{ $cat->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Linked Course</label>
                <select name="course_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    <option value="">None</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ old('course_id') == $c->id ? 'selected' : '' }}>{{ $c->title_en }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Starts At *</label>
                <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Duration (minutes) *</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 60) }}" min="15" max="480" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Instructor Name</label>
            <input type="text" name="instructor_name" value="{{ old('instructor_name') }}"
                   placeholder="e.g. Ram Bahadur Shrestha"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Stream / YouTube URL</label>
            <input type="url" name="stream_url" value="{{ old('stream_url') }}"
                   placeholder="https://youtube.com/live/... or Zoom link"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Recording URL (fill after session ends)</label>
            <input type="url" name="recording_url" value="{{ old('recording_url') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
        </div>

        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_free" value="1" {{ old('is_free', true) ? 'checked' : '' }}
                   class="w-4 h-4 text-[#4A5D4A] rounded border-gray-300">
            <span class="text-sm font-medium text-gray-700">Free to attend</span>
        </label>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] font-medium text-sm">Schedule Session</button>
            <a href="{{ route('admin.learning.live-sessions.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection
