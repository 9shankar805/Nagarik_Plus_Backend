@extends('admin.layouts.app')
@section('title', 'Edit Live Session')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.learning.live-sessions.index') }}" class="text-sm text-blue-600 hover:text-blue-700">← Back</a>
    <h2 class="text-2xl font-bold text-gray-900 mt-2">Edit: {{ $liveSession->title_en }}</h2>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl space-y-5">
    <form action="{{ route('admin.learning.live-sessions.update', $liveSession) }}" method="POST">
        @csrf @method('PUT')

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Title (EN) *</label>
                <input type="text" name="title_en" value="{{ old('title_en', $liveSession->title_en) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Title (NP)</label>
                <input type="text" name="title_np" value="{{ old('title_np', $liveSession->title_np) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="2"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">{{ old('description', $liveSession->description) }}</textarea>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Type *</label>
                <select name="type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    <option value="live" {{ old('type', $liveSession->type) === 'live' ? 'selected' : '' }}>🔴 Live Class</option>
                    <option value="recorded" {{ old('type', $liveSession->type) === 'recorded' ? 'selected' : '' }}>📹 Recorded</option>
                    <option value="doubt_clearing" {{ old('type', $liveSession->type) === 'doubt_clearing' ? 'selected' : '' }}>❓ Doubt Clearing</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Status *</label>
                <select name="status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    @foreach(['scheduled','live','ended','cancelled'] as $s)
                        <option value="{{ $s }}" {{ old('status', $liveSession->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                <select name="learning_category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    <option value="">Global</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('learning_category_id', $liveSession->learning_category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->icon }} {{ $cat->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Starts At *</label>
                <input type="datetime-local" name="starts_at" required
                       value="{{ old('starts_at', $liveSession->starts_at->format('Y-m-d\TH:i')) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Duration (minutes) *</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $liveSession->duration_minutes) }}" min="15" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Instructor Name</label>
            <input type="text" name="instructor_name" value="{{ old('instructor_name', $liveSession->instructor_name) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Stream URL (Live) / YouTube</label>
            <input type="url" name="stream_url" value="{{ old('stream_url', $liveSession->stream_url) }}"
                   placeholder="https://youtube.com/watch?v=..."
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Recording URL (after live)</label>
            <input type="url" name="recording_url" value="{{ old('recording_url', $liveSession->recording_url) }}"
                   placeholder="https://youtube.com/watch?v=..."
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
        </div>

        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_free" value="1" {{ old('is_free', $liveSession->is_free) ? 'checked' : '' }}
                   class="w-4 h-4 text-[#4A5D4A] rounded border-gray-300">
            <span class="text-sm font-medium text-gray-700">Free to attend</span>
        </label>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] font-medium text-sm">Update Session</button>
            <a href="{{ route('admin.learning.live-sessions.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection
