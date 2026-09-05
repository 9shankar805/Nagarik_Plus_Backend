@extends('admin.layouts.app')

@section('title', 'Leaderboard')
@section('subtitle', $competition->title)

@section('content')
<div class="mb-6 flex items-start justify-between">
    <div>
        <a href="{{ route('admin.learning.competitions.show', $competition) }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Overview</a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">🏆 Leaderboard — {{ $competition->title }}</h2>
    </div>
    <div class="flex gap-2 mt-6">
        @if(in_array($competition->status, ['ongoing','completed']))
        <form action="{{ route('admin.learning.competitions.compute-leaderboard', $competition) }}" method="POST">
            @csrf
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                🔢 Recompute Ranks
            </button>
        </form>
        @endif
        <a href="{{ route('admin.learning.competitions.export', $competition) }}"
           class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">
            📥 Export CSV
        </a>
    </div>
</div>

@if($entries->isEmpty())
    <div class="bg-white rounded-xl border border-gray-100 p-10 text-center text-gray-400">
        <p class="text-lg mb-2">No leaderboard data yet.</p>
        @if(in_array($competition->status, ['ongoing','completed']))
            <p class="text-sm">Click "Recompute Ranks" after participants have submitted their answers.</p>
        @else
            <p class="text-sm">Leaderboard will be available once the competition ends.</p>
        @endif
    </div>
@else
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">Rank</th>
                <th class="py-3 px-4">Participant</th>
                <th class="py-3 px-4 text-center">Score</th>
                <th class="py-3 px-4 text-center">Correct</th>
                <th class="py-3 px-4 text-center">Time Taken</th>
                <th class="py-3 px-4 text-right">Prize Won</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($entries as $entry)
            @php
                $medal = match($entry->rank) { 1 => '🥇', 2 => '🥈', 3 => '🥉', default => null };
                $rowBg = $entry->rank <= 3 ? 'bg-amber-50' : '';
            @endphp
            <tr class="hover:bg-gray-50 {{ $rowBg }}">
                <td class="py-3 px-4">
                    <div class="flex items-center gap-2">
                        @if($medal)
                            <span class="text-xl">{{ $medal }}</span>
                        @else
                            <span class="w-7 h-7 flex items-center justify-center bg-gray-100 rounded-full text-xs font-bold text-gray-600">
                                {{ $entry->rank }}
                            </span>
                        @endif
                    </div>
                </td>
                <td class="py-3 px-4">
                    <div class="flex items-center gap-3">
                        @if($entry->user?->avatar)
                            <img src="{{ $entry->user->avatar }}" class="w-8 h-8 rounded-full object-cover">
                        @else
                            <div class="w-8 h-8 rounded-full bg-[#4A5D4A] flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr($entry->user?->name ?? 'U', 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <div class="font-semibold text-gray-900">{{ $entry->user?->name ?? 'Deleted User' }}</div>
                            <div class="text-xs text-gray-400">{{ $entry->user?->email }}</div>
                        </div>
                    </div>
                </td>
                <td class="py-3 px-4 text-center">
                    <span class="text-lg font-bold {{ $entry->score_percentage >= 80 ? 'text-green-600' : ($entry->score_percentage >= 60 ? 'text-blue-600' : 'text-red-500') }}">
                        {{ $entry->score_percentage }}%
                    </span>
                </td>
                <td class="py-3 px-4 text-center font-semibold text-gray-700">{{ $entry->correct_answers }}</td>
                <td class="py-3 px-4 text-center text-sm font-mono text-gray-600">
                    {{ $entry->time_taken_seconds ? gmdate('i:s', $entry->time_taken_seconds) : '—' }}
                </td>
                <td class="py-3 px-4 text-right font-bold {{ $entry->prize_won > 0 ? 'text-green-700' : 'text-gray-400' }}">
                    {{ $entry->prize_won > 0 ? 'NPR '.number_format($entry->prize_won) : '—' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">{{ $entries->links() }}</div>
</div>
@endif
@endsection
