@extends('admin.layouts.app')

@section('title', 'Student Progress — ' . $student->name)
@section('subtitle', 'Parent monitoring view for this student')

@section('content')
<div class="mb-6 flex justify-between items-start">
    <div>
        <a href="{{ route('admin.learning.parental.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Parental Monitoring</a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">{{ $student->name }}</h2>
        <p class="text-sm text-gray-500">{{ $student->email }} &bull; {{ $student->phone }}</p>
        <p class="text-xs text-gray-400 mt-1">
            Parent: <span class="font-medium text-gray-600">{{ $link->parent?->name }}</span>
            ({{ $link->parent?->email }})
        </p>
    </div>
    <span class="px-3 py-1 text-xs font-semibold rounded-full
        {{ $link->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
        Link: {{ ucfirst($link->status) }}
    </span>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
        <div class="text-3xl font-bold text-[#4A5D4A]">{{ $totalTests }}</div>
        <div class="text-sm text-gray-500 mt-1">Tests Taken</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
        <div class="text-3xl font-bold text-green-600">{{ $passed }}</div>
        <div class="text-sm text-gray-500 mt-1">Tests Passed</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
        <div class="text-3xl font-bold text-blue-600">{{ number_format($avgScore, 1) }}%</div>
        <div class="text-sm text-gray-500 mt-1">Avg Score</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
        <div class="text-3xl font-bold text-orange-500">
            {{ $streak?->current_streak ?? 0 }}🔥
        </div>
        <div class="text-sm text-gray-500 mt-1">Day Streak</div>
    </div>
</div>

{{-- Recent test attempts --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-semibold text-gray-800">Recent Test Attempts</h3>
        <span class="text-xs text-gray-400">Last 20 attempts</span>
    </div>
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">Test</th>
                <th class="py-3 px-4 text-center">Score</th>
                <th class="py-3 px-4 text-center">Correct</th>
                <th class="py-3 px-4 text-center">Result</th>
                <th class="py-3 px-4 text-center">Time Taken</th>
                <th class="py-3 px-4">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($recentAttempts as $attempt)
            <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4">
                    <div class="font-medium text-gray-900">
                        {{ $attempt->mockTest?->title ?? 'Competition / Practice' }}
                    </div>
                    @if($attempt->mockTest)
                        <div class="text-xs text-gray-400">{{ $attempt->mockTest->question_count }}Q &bull; {{ $attempt->mockTest->duration_minutes }}min</div>
                    @endif
                </td>
                <td class="py-3 px-4 text-center">
                    <span class="font-bold {{ $attempt->score_percentage >= 60 ? 'text-green-600' : 'text-red-500' }}">
                        {{ $attempt->score_percentage }}%
                    </span>
                </td>
                <td class="py-3 px-4 text-center text-gray-600">
                    {{ $attempt->correct_answers }} / {{ $attempt->total_questions }}
                </td>
                <td class="py-3 px-4 text-center">
                    @if($attempt->passed)
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full font-semibold">Pass</span>
                    @else
                        <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs rounded-full font-semibold">Fail</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-center text-xs text-gray-500">
                    {{ gmdate('i:s', $attempt->time_taken_seconds ?? 0) }}
                </td>
                <td class="py-3 px-4 text-xs text-gray-400">
                    {{ $attempt->completed_at?->format('d M Y, H:i') ?? $attempt->created_at->format('d M Y') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-8 text-center text-gray-400">No test attempts yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
