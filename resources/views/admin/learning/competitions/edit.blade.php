@extends('admin.layouts.app')

@section('title', 'Edit Competition')
@section('subtitle', $competition->title)

@section('content')
<div class="mb-6 flex items-start justify-between">
    <div>
        <a href="{{ route('admin.learning.competitions.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Competitions</a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">Edit: {{ Str::limit($competition->title, 55) }}</h2>
    </div>
    <div class="flex gap-2 mt-6">
        <a href="{{ route('admin.learning.competitions.registrations', $competition) }}"
           class="px-3 py-2 bg-blue-50 text-blue-700 rounded-lg text-xs font-medium hover:bg-blue-100">Registrations</a>
        <a href="{{ route('admin.learning.competitions.leaderboard', $competition) }}"
           class="px-3 py-2 bg-purple-50 text-purple-700 rounded-lg text-xs font-medium hover:bg-purple-100">Leaderboard</a>
        <a href="{{ route('admin.learning.competitions.show', $competition) }}"
           class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-200">Overview</a>
    </div>
</div>

<form action="{{ route('admin.learning.competitions.update', $competition) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="grid grid-cols-3 gap-6">

        {{-- Main --}}
        <div class="col-span-2 space-y-5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="font-semibold text-gray-800 border-b pb-2">Basic Information</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Title (English) <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $competition->title) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Title (Nepali)</label>
                        <input type="text" name="title_np" value="{{ old('title_np', $competition->title_np) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Description (English)</label>
                        <textarea name="description" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">{{ old('description', $competition->description) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Description (Nepali)</label>
                        <textarea name="description_np" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">{{ old('description_np', $competition->description_np) }}</textarea>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Banner Image</label>
                    @if($competition->banner_url)
                        <img src="{{ $competition->banner_url }}" class="h-20 rounded-lg object-cover mb-2 border">
                    @endif
                    <input type="file" name="banner_file" accept="image/jpeg,image/png,image/jpg,image/webp"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="font-semibold text-gray-800 border-b pb-2">Schedule</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Registration Opens</label>
                        <input type="datetime-local" name="registration_open_at"
                               value="{{ old('registration_open_at', $competition->registration_open_at?->format('Y-m-d\TH:i')) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Registration Closes</label>
                        <input type="datetime-local" name="registration_close_at"
                               value="{{ old('registration_close_at', $competition->registration_close_at?->format('Y-m-d\TH:i')) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Starts At <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="starts_at" required
                               value="{{ old('starts_at', $competition->starts_at->format('Y-m-d\TH:i')) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Ends At <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="ends_at" required
                               value="{{ old('ends_at', $competition->ends_at->format('Y-m-d\TH:i')) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="font-semibold text-gray-800 border-b pb-2">Prize Pool & Entry</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Entry Fee (NPR)</label>
                        <input type="number" name="entry_fee" value="{{ old('entry_fee', $competition->entry_fee) }}" min="0" step="0.01"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Prize Pool (NPR)</label>
                        <input type="number" name="prize_pool" value="{{ old('prize_pool', $competition->prize_pool) }}" min="0" step="0.01"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Max Participants</label>
                        <input type="number" name="max_participants" value="{{ old('max_participants', $competition->max_participants) }}" min="2"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Prize Distribution (JSON)</label>
                    <textarea name="prize_distribution" rows="4"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-[#4A5D4A]">{{ old('prize_distribution', $competition->prize_distribution ? json_encode($competition->prize_distribution, JSON_PRETTY_PRINT) : '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-span-1 space-y-5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
                <h3 class="font-semibold text-gray-800 border-b pb-2">Settings</h3>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                        @foreach(['draft','open','ongoing','completed','cancelled'] as $s)
                            <option value="{{ $s }}" {{ old('status', $competition->status) == $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                    <select name="learning_category_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('learning_category_id', $competition->learning_category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->icon }} {{ $cat->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Mock Test</label>
                    <select name="mock_test_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                        @foreach($mockTests as $test)
                            <option value="{{ $test->id }}"
                                {{ old('mock_test_id', $competition->mock_test_id) == $test->id ? 'selected' : '' }}>
                                {{ $test->title }} ({{ $test->question_count }}Q · {{ $test->duration_minutes }}min)
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <button type="submit"
                        class="w-full px-5 py-2.5 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] transition font-medium text-sm">
                    Update Competition
                </button>
                <a href="{{ route('admin.learning.competitions.index') }}"
                   class="w-full text-center px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</form>
@endsection
