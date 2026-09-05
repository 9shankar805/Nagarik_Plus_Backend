@extends('admin.layouts.app')

@section('title', 'Mock Test Stats')
@section('subtitle', $mockTest->title)

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="{{ route('admin.learning.mock-tests.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Mock Tests</a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">{{ $mockTest->title }}</h2>
    </div>
    <a href="{{ route('admin.learning.mock-tests.edit', $mockTest) }}"
       class="px-4 py-2 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] transition font-medium text-sm">Edit Test</a>
    <a href="{{ route('admin.learning.mock-tests.questions', $mockTest) }}"
       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm flex items-center gap-1">
        📋 Assign Questions
        @if($mockTest->use_fixed_questions)
            <span class="ml-1 px-1.5 py-0.5 bg-white text-blue-700 rounded text-xs font-bold">Fixed</span>
        @endif
    </a>
</div>

{{-- Stats --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-100 p-5 text-center">
        <div class="text-3xl font-bold text-[#4A5D4A]">{{ $attemptsCount }}</div>
        <div class="text-sm text-gray-500 mt-1">Total Attempts</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-5 text-center">
        <div class="text-3xl font-bold text-green-600">{{ $passedCount }}</div>
        <div class="text-sm text-gray-500 mt-1">Passed</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-5 text-center">
        <div class="text-3xl font-bold text-blue-600">{{ round($avgScore, 1) }}%</div>
        <div class="text-sm text-gray-500 mt-1">Avg. Score</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-5 text-center">
        <div class="text-3xl font-bold text-gray-700">
            {{ $attemptsCount > 0 ? round(($passedCount / $attemptsCount) * 100) : 0 }}%
        </div>
        <div class="text-sm text-gray-500 mt-1">Pass Rate</div>
    </div>
</div>

{{-- Test details --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="col-span-1 bg-white rounded-xl border border-gray-100 p-5 space-y-2 text-sm">
        <h3 class="font-semibold text-gray-800 border-b pb-2 mb-3">Test Configuration</h3>
        <div class="flex justify-between"><span class="text-gray-500">Questions</span><span class="font-semibold">{{ $mockTest->question_count }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Duration</span><span class="font-semibold">{{ $mockTest->duration_minutes }} min</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Pass %</span><span class="font-semibold">{{ $mockTest->pass_percentage }}%</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Negative Marking</span>
            <span class="font-semibold">{{ $mockTest->negative_marking ? '-'.$mockTest->negative_value.' per wrong' : 'None' }}</span>
        </div>
        <div class="flex justify-between"><span class="text-gray-500">Category</span>
            <span class="font-semibold">{{ $mockTest->learningCategory?->name_en ?? $mockTest->category }}</span>
        </div>
        <div class="flex justify-between"><span class="text-gray-500">Question Mode</span>
            <span class="font-semibold {{ $mockTest->use_fixed_questions ? 'text-blue-700' : 'text-gray-600' }}">
                {{ $mockTest->use_fixed_questions ? '📋 Fixed Set' : '🔀 Random Pool' }}
            </span>
        </div>
    </div>

    {{-- Recent attempts --}}
    <div class="col-span-2 bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 font-semibold text-gray-800">Recent Attempts (last 20)</div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="py-2 px-4 text-left">User</th>
                    <th class="py-2 px-4 text-center">Score</th>
                    <th class="py-2 px-4 text-center">Percentile</th>
                    <th class="py-2 px-4 text-center">Result</th>
                    <th class="py-2 px-4 text-center">Time</th>
                    <th class="py-2 px-4 text-left">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($recentAttempts as $attempt)
                <tr class="hover:bg-gray-50">
                    <td class="py-2 px-4">
                        <div class="font-medium text-gray-800">{{ $attempt->user?->name ?? 'Deleted User' }}</div>
                        <div class="text-xs text-gray-400">{{ $attempt->user?->email }}</div>
                    </td>
                    <td class="py-2 px-4 text-center font-semibold {{ $attempt->score_percentage >= $mockTest->pass_percentage ? 'text-green-600' : 'text-red-500' }}">
                        {{ $attempt->score_percentage }}%
                    </td>
                    <td class="py-2 px-4 text-center">
                        @if($attempt->percentile_score !== null)
                            <span class="text-xs font-bold {{ $attempt->percentile_score >= 75 ? 'text-green-600' : ($attempt->percentile_score >= 50 ? 'text-blue-500' : 'text-gray-500') }}">
                                {{ number_format($attempt->percentile_score, 1) }}th
                            </span>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="py-2 px-4 text-center">
                        @if($attempt->passed)
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Pass</span>
                        @else
                            <span class="px-2 py-0.5 bg-red-100 text-red-600 rounded-full text-xs font-semibold">Fail</span>
                        @endif
                    </td>
                    <td class="py-2 px-4 text-center text-xs text-gray-500">
                        {{ $attempt->time_taken_seconds ? gmdate('i:s', $attempt->time_taken_seconds) : '—' }}
                    </td>
                    <td class="py-2 px-4 text-xs text-gray-500">
                        {{ $attempt->completed_at?->format('d M Y') }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-6 text-center text-gray-400">No attempts yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Percentile & Difficulty Analytics --}}
<div class="grid grid-cols-2 gap-5 mb-6">

    {{-- Percentile distribution chart --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="font-semibold text-gray-800">📊 Percentile Distribution</h3>
                <p class="text-xs text-gray-400">How takers are spread across score percentiles</p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold text-blue-600">{{ number_format($avgPercentile, 1) }}</div>
                <div class="text-xs text-gray-400">Avg Percentile</div>
            </div>
        </div>
        @php $maxBucket = max(array_values($percentileDistribution) ?: [1]); @endphp
        <div class="space-y-1.5">
            @foreach($percentileDistribution as $from => $cnt)
            <div class="flex items-center gap-2 text-xs">
                <span class="w-16 text-right text-gray-500">{{ $from }}–{{ $from + 9 }}th</span>
                <div class="flex-1 bg-gray-100 rounded-full h-4 relative">
                    @if($cnt > 0)
                    <div class="h-4 rounded-full flex items-center justify-end pr-1.5
                        {{ $from >= 80 ? 'bg-green-500' : ($from >= 50 ? 'bg-blue-400' : 'bg-gray-400') }}"
                         style="width: {{ $maxBucket > 0 ? round(($cnt/$maxBucket)*100) : 0 }}%">
                        <span class="text-white text-xs font-bold">{{ $cnt }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @if(array_sum($percentileDistribution) === 0)
        <p class="text-center text-gray-400 text-xs mt-4">No percentile data yet. Run <code class="bg-gray-100 px-1 rounded">php artisan learning:compute-percentiles</code> or wait for the next attempt.</p>
        @endif
    </div>

    {{-- Difficulty accuracy --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5">
        <div class="mb-4">
            <h3 class="font-semibold text-gray-800">🎯 Avg Accuracy by Difficulty</h3>
            <p class="text-xs text-gray-400">Across all scored attempts with breakdown data</p>
        </div>
        <div class="space-y-5 mt-6">
            @foreach(['easy' => ['label' => 'Easy', 'color' => 'bg-green-500', 'text' => 'text-green-600'],
                       'medium' => ['label' => 'Medium', 'color' => 'bg-yellow-400', 'text' => 'text-yellow-600'],
                       'hard' => ['label' => 'Hard', 'color' => 'bg-red-500', 'text' => 'text-red-600']] as $diff => $meta)
            @php $acc = $avgDifficultyAccuracy[$diff] ?? null; @endphp
            <div>
                <div class="flex justify-between text-sm mb-1.5">
                    <span class="font-medium text-gray-700">{{ $meta['label'] }}</span>
                    <span class="font-bold {{ $meta['text'] }}">
                        {{ $acc !== null ? $acc . '%' : 'No data' }}
                    </span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3">
                    <div class="{{ $meta['color'] }} h-3 rounded-full transition-all"
                         style="width: {{ $acc ?? 0 }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
        <p class="text-xs text-gray-400 mt-6">
            💡 Difficulty breakdown is computed live on each submission and nightly via
            <code class="bg-gray-100 px-1 rounded">learning:compute-percentiles</code>
        </p>
    </div>
</div>

{{-- Most-Wrong Questions (Wrong-only re-test analytics) --}}
@if(count($topWrongIds) > 0)
<div class="bg-white rounded-xl border border-gray-100 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h3 class="font-semibold text-gray-800">❌ Most Frequently Wrong Questions</h3>
            <p class="text-xs text-gray-400 mt-0.5">From the last 20 attempts. These appear in the "Wrong-only Re-test" for students.</p>
        </div>
        <span class="text-xs text-gray-400">Top {{ count($topWrongIds) }} questions</span>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
            <tr>
                <th class="py-2 px-4 text-left">Question</th>
                <th class="py-2 px-4 text-center">Difficulty</th>
                <th class="py-2 px-4 text-center">Wrong Count</th>
                <th class="py-2 px-4 text-center">Error Rate</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($topWrongIds as $qId)
            @php
                $q = $topWrongQuestions[$qId] ?? null;
                $wrongCnt = $wrongFrequency[$qId] ?? 0;
                $errorPct = $attemptsCount > 0 ? round(($wrongCnt / $attemptsCount) * 100) : 0;
            @endphp
            @if($q)
            <tr class="hover:bg-gray-50">
                <td class="py-3 px-4 max-w-md">
                    <p class="text-gray-800 text-sm">{{ Str::limit($q->question, 120) }}</p>
                </td>
                <td class="py-3 px-4 text-center">
                    <span class="px-2 py-0.5 rounded text-xs font-medium
                        {{ $q->difficulty === 'easy' ? 'bg-green-100 text-green-700' : ($q->difficulty === 'medium' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                        {{ ucfirst($q->difficulty) }}
                    </span>
                </td>
                <td class="py-3 px-4 text-center font-bold text-red-600">{{ $wrongCnt }}</td>
                <td class="py-3 px-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <div class="w-20 bg-gray-100 rounded-full h-1.5">
                            <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ min($errorPct, 100) }}%"></div>
                        </div>
                        <span class="text-xs text-red-600 font-semibold">{{ $errorPct }}%</span>
                    </div>
                </td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
