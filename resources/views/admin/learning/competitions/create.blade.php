@extends('admin.layouts.app')

@section('title', 'Create Competition')
@section('subtitle', 'Set up a new prize pool competition')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.learning.competitions.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Competitions</a>
    <h2 class="text-2xl font-bold text-gray-900 mt-2">Create Competition</h2>
</div>

<form action="{{ route('admin.learning.competitions.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-3 gap-6">

        {{-- Main --}}
        <div class="col-span-2 space-y-5">

            {{-- Basic Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="font-semibold text-gray-800 border-b pb-2">Basic Information</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Title (English) <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                               placeholder="e.g. Loksewa Grand Challenge 2026"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] @error('title') border-red-400 @enderror">
                        @error('title')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Title (Nepali)</label>
                        <input type="text" name="title_np" value="{{ old('title_np') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Description (English)</label>
                        <textarea name="description" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Description (Nepali)</label>
                        <textarea name="description_np" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">{{ old('description_np') }}</textarea>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Banner Image</label>
                    <input type="file" name="banner_file" accept="image/jpeg,image/png,image/jpg,image/webp"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <p class="text-xs text-gray-400 mt-1">Optional. Max 5MB.</p>
                </div>
            </div>

            {{-- Schedule --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="font-semibold text-gray-800 border-b pb-2">Schedule</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Registration Opens</label>
                        <input type="datetime-local" name="registration_open_at" value="{{ old('registration_open_at') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                        <p class="text-xs text-gray-400 mt-1">Leave blank to open immediately on status change.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Registration Closes</label>
                        <input type="datetime-local" name="registration_close_at" value="{{ old('registration_close_at') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Competition Starts <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] @error('starts_at') border-red-400 @enderror">
                        @error('starts_at')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Competition Ends <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] @error('ends_at') border-red-400 @enderror">
                        @error('ends_at')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Prize Pool --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="font-semibold text-gray-800 border-b pb-2">Prize Pool & Entry</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Entry Fee (NPR)</label>
                        <input type="number" name="entry_fee" value="{{ old('entry_fee', 0) }}" min="0" step="0.01"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                        <p class="text-xs text-gray-400 mt-1">Set 0 for free competitions.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Total Prize Pool (NPR)</label>
                        <input type="number" name="prize_pool" value="{{ old('prize_pool', 0) }}" min="0" step="0.01"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Max Participants</label>
                        <input type="number" name="max_participants" value="{{ old('max_participants') }}" min="2" placeholder="Unlimited"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Prize Distribution (JSON)</label>
                    <textarea name="prize_distribution" rows="5"
                              placeholder='[{"rank":1,"label":"1st Prize","amount":5000},{"rank":2,"label":"2nd Prize","amount":2000},{"rank":3,"label":"3rd Prize","amount":1000}]'
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-[#4A5D4A]">{{ old('prize_distribution') }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">JSON array: rank, label, amount. Leave empty for no prizes.</p>
                    @error('prize_distribution')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-span-1 space-y-5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
                <h3 class="font-semibold text-gray-800 border-b pb-2">Settings</h3>

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
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Mock Test (Question Set) <span class="text-red-500">*</span></label>
                    <select name="mock_test_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] @error('mock_test_id') border-red-400 @enderror">
                        <option value="">Select mock test</option>
                        @foreach($mockTests as $test)
                            <option value="{{ $test->id }}" {{ old('mock_test_id') == $test->id ? 'selected' : '' }}>
                                {{ $test->title }} ({{ $test->question_count }}Q · {{ $test->duration_minutes }}min)
                            </option>
                        @endforeach
                    </select>
                    @error('mock_test_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    <p class="text-xs text-gray-400 mt-1">Questions and duration will be taken from this mock test.</p>
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-xs text-amber-700 space-y-1">
                <p class="font-semibold">Competition Lifecycle</p>
                <p>1. Created as <strong>Draft</strong></p>
                <p>2. Change to <strong>Open</strong> → users register</p>
                <p>3. Auto-flips to <strong>Ongoing</strong> at starts_at</p>
                <p>4. Auto-flips to <strong>Completed</strong> at ends_at</p>
                <p>5. Admin computes leaderboard & announces winners</p>
            </div>

            <div class="flex flex-col gap-3">
                <button type="submit"
                        class="w-full px-5 py-2.5 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] transition font-medium text-sm">
                    Create Competition
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
