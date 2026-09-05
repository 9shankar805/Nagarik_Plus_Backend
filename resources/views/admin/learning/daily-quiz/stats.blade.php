@extends('admin.layouts.app')
@section('title', 'Daily Quiz Stats')

@section('content')
<div class="mb-6 flex items-start justify-between">
    <div>
        <a href="{{ route('admin.learning.daily-quiz.index') }}" class="text-sm text-blue-600 hover:text-blue-700">← Back</a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">📊 Stats — {{ $dailyQuiz->quiz_date->format('d M Y') }}</h2>
        <p class="text-sm text-gray-500 mt-1 truncate max-w-xl">{{ $dailyQuiz->question?->question }}</p>
    </div>
</div>

<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-100 p-5 text-center">
        <div class="text-3xl font-bold text-[#4A5D4A]">{{ $totalEntries }}</div>
        <div class="text-sm text-gray-500 mt-1">Participants</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-5 text-center">
        <div class="text-3xl font-bold text-green-600">{{ $correctEntries }}</div>
        <div class="text-sm text-gray-500 mt-1">Correct Answers</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-5 text-center">
        <div class="text-3xl font-bold text-blue-600">
            {{ $totalEntries > 0 ? round(($correctEntries / $totalEntries) * 100) : 0 }}%
        </div>
        <div class="text-sm text-gray-500 mt-1">Accuracy Rate</div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-3 border-b font-semibold text-gray-800">Participant Responses</div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
            <tr>
                <th class="py-2 px-4 text-left">User</th>
                <th class="py-2 px-4 text-center">Selected</th>
                <th class="py-2 px-4 text-center">Result</th>
                <th class="py-2 px-4 text-left">Answered At</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($entries as $entry)
            <tr class="hover:bg-gray-50">
                <td class="py-2 px-4">
                    <div class="font-medium text-gray-800">{{ $entry->user?->name ?? 'Deleted' }}</div>
                    <div class="text-xs text-gray-400">{{ $entry->user?->email }}</div>
                </td>
                <td class="py-2 px-4 text-center text-xs font-mono">
                    Option {{ $entry->selected_index >= 0 ? chr(65 + $entry->selected_index) : 'Skipped' }}
                </td>
                <td class="py-2 px-4 text-center">
                    @if($entry->selected_index < 0)
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full text-xs">Skipped</span>
                    @elseif($entry->is_correct)
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">✓ Correct</span>
                    @else
                        <span class="px-2 py-0.5 bg-red-100 text-red-600 rounded-full text-xs font-semibold">✗ Wrong</span>
                    @endif
                </td>
                <td class="py-2 px-4 text-xs text-gray-500">{{ $entry->answered_at?->format('d M Y, h:i A') }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="py-6 text-center text-gray-400">No responses yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t">{{ $entries->links() }}</div>
</div>
@endsection
