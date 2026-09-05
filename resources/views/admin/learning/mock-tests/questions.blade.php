@extends('admin.layouts.app')

@section('title', 'Assign Questions — ' . $mockTest->title)
@section('subtitle', 'Pick specific questions for this mock test instead of random pool')

@section('content')
<div class="mb-6 flex justify-between items-start">
    <div>
        <a href="{{ route('admin.learning.mock-tests.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Mock Tests</a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">Assign Questions: {{ $mockTest->title }}</h2>
        <p class="text-sm text-gray-500 mt-1">
            Category: <strong>{{ $mockTest->learningCategory?->name_en }}</strong> &bull;
            Default count: <strong>{{ $mockTest->question_count }}</strong> &bull;
            Currently assigned: <strong>{{ count($assignedIds) }}</strong>
        </p>
    </div>
    <div class="flex items-center gap-3">
        @if($mockTest->use_fixed_questions)
            <span class="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">Fixed Mode ON</span>
        @else
            <span class="px-3 py-1.5 bg-gray-100 text-gray-500 rounded-full text-xs font-medium">Random Pool Mode</span>
        @endif
    </div>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
    {{ session('success') }}
</div>
@endif

<form action="{{ route('admin.learning.mock-tests.questions.sync', $mockTest) }}" method="POST" id="qform">
    @csrf @method('PUT')

    <div class="grid grid-cols-3 gap-6">

        {{-- Left: Available questions --}}
        <div class="col-span-2 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                <div>
                    <h3 class="font-semibold text-gray-800">Available Questions ({{ $available->count() }})</h3>
                    <p class="text-xs text-gray-400">From the <strong>{{ $mockTest->category }}</strong> category. Check to assign.</p>
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="selectAll()" class="text-xs text-blue-600 hover:underline font-medium">Select All</button>
                    <span class="text-gray-300">|</span>
                    <button type="button" onclick="clearAll()" class="text-xs text-red-500 hover:underline font-medium">Clear All</button>
                </div>
            </div>

            {{-- Search filter --}}
            <div class="px-5 py-3 border-b border-gray-100">
                <input type="text" id="q_search" placeholder="Filter questions by keyword..."
                       oninput="filterQuestions(this.value)"
                       class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
            </div>

            <div class="overflow-y-auto max-h-[600px]" id="question-list">
                @forelse($available as $q)
                <label class="q-row flex items-start gap-3 px-5 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-50 transition"
                       data-text="{{ strtolower($q->question) }}">
                    <input type="checkbox" name="question_ids[]" value="{{ $q->id }}"
                           {{ in_array($q->id, $assignedIds) ? 'checked' : '' }}
                           onchange="updateCount()"
                           class="mt-0.5 w-4 h-4 text-[#4A5D4A] rounded border-gray-300 shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-800 leading-snug">{{ $q->question }}</p>
                        <div class="flex gap-2 mt-1">
                            <span class="text-xs px-1.5 py-0.5 rounded font-medium
                                {{ $q->difficulty === 'easy' ? 'bg-green-100 text-green-700' : ($q->difficulty === 'medium' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst($q->difficulty) }}
                            </span>
                            @if($q->learning_chapter_id)
                                <span class="text-xs text-gray-400">Has chapter link</span>
                            @endif
                        </div>
                    </div>
                    <span class="text-xs text-gray-300 font-mono shrink-0">#{{ $q->id }}</span>
                </label>
                @empty
                <div class="px-5 py-10 text-center text-gray-400 text-sm">
                    No active questions found for category <strong>{{ $mockTest->category }}</strong>.
                    <a href="{{ route('admin.quiz.create') }}" class="text-blue-600 hover:underline">Add questions</a>.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Right: Settings + submit --}}
        <div class="col-span-1 space-y-5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
                <h3 class="font-semibold text-gray-800 border-b pb-2">Question Mode</h3>

                <div id="selected-count" class="text-3xl font-bold text-[#4A5D4A] text-center">
                    {{ count($assignedIds) }}
                    <span class="text-sm font-normal text-gray-500 block">selected</span>
                </div>

                <div class="bg-blue-50 rounded-lg p-3 space-y-3">
                    <label class="flex items-start gap-2 cursor-pointer">
                        <input type="checkbox" name="use_fixed" value="1" id="use_fixed_cb"
                               {{ $mockTest->use_fixed_questions ? 'checked' : '' }}
                               class="mt-0.5 w-4 h-4 text-[#4A5D4A] rounded border-gray-300">
                        <div>
                            <span class="text-sm font-semibold text-gray-800 block">Enable Fixed Question Mode</span>
                            <span class="text-xs text-gray-500">When ON: only the selected questions are used, in order. When OFF: system picks randomly from the category pool.</span>
                        </div>
                    </label>
                </div>

                <div class="text-xs text-gray-500 space-y-1 bg-gray-50 rounded-lg p-3">
                    <p><strong>Random mode:</strong> questions are pulled at runtime from all active questions in the category.</p>
                    <p class="mt-1"><strong>Fixed mode:</strong> exact questions you select here are given to every user, in the order shown.</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-3">
                <h3 class="font-semibold text-gray-800 border-b pb-2">Difficulty Breakdown</h3>
                <div class="space-y-2 text-sm" id="difficulty-breakdown">
                    <div class="flex justify-between"><span class="text-gray-500">Easy</span><span class="font-medium text-green-600" id="cnt-easy">{{ $available->where('difficulty','easy')->count() }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Medium</span><span class="font-medium text-yellow-600" id="cnt-medium">{{ $available->where('difficulty','medium')->count() }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Hard</span><span class="font-medium text-red-600" id="cnt-hard">{{ $available->where('difficulty','hard')->count() }}</span></div>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <button type="submit"
                        class="w-full px-5 py-2.5 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] transition font-medium text-sm">
                    Save Question Assignment
                </button>
                <a href="{{ route('admin.learning.mock-tests.index') }}"
                   class="w-full text-center px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</form>

<script>
function updateCount() {
    const checked = document.querySelectorAll('input[name="question_ids[]"]:checked').length;
    document.getElementById('selected-count').innerHTML =
        checked + '<span class="text-sm font-normal text-gray-500 block">selected</span>';
}

function selectAll() {
    document.querySelectorAll('.q-row:not([style*="display: none"]) input[type="checkbox"]')
        .forEach(cb => { cb.checked = true; });
    updateCount();
}

function clearAll() {
    document.querySelectorAll('input[name="question_ids[]"]').forEach(cb => { cb.checked = false; });
    updateCount();
}

function filterQuestions(term) {
    const t = term.toLowerCase();
    document.querySelectorAll('.q-row').forEach(row => {
        row.style.display = row.dataset.text.includes(t) ? '' : 'none';
    });
}

// Init count on load
updateCount();
</script>
@endsection
