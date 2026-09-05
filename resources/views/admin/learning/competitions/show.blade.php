@extends('admin.layouts.app')

@section('title', 'Competition Overview')
@section('subtitle', $competition->title)

@section('content')
<div class="mb-6 flex items-start justify-between">
    <div>
        <a href="{{ route('admin.learning.competitions.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Competitions</a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">{{ $competition->title }}</h2>
        @php
            $statusColors = ['draft'=>'bg-gray-100 text-gray-600','open'=>'bg-blue-100 text-blue-700','ongoing'=>'bg-green-100 text-green-700','completed'=>'bg-purple-100 text-purple-700','cancelled'=>'bg-red-100 text-red-600'];
            $color = $statusColors[$competition->status] ?? 'bg-gray-100 text-gray-600';
        @endphp
        <span class="mt-2 inline-block px-3 py-1 text-xs font-semibold rounded-full {{ $color }}">{{ ucfirst($competition->status) }}</span>
    </div>
    <div class="flex gap-2 mt-6">
        <a href="{{ route('admin.learning.competitions.edit', $competition) }}"
           class="px-4 py-2 bg-[#4A5D4A] text-white rounded-lg text-sm font-medium hover:bg-[#3F523F]">Edit</a>
        <a href="{{ route('admin.learning.competitions.registrations', $competition) }}"
           class="px-4 py-2 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-100">Registrations</a>
        <a href="{{ route('admin.learning.competitions.leaderboard', $competition) }}"
           class="px-4 py-2 bg-purple-50 text-purple-700 rounded-lg text-sm font-medium hover:bg-purple-100">Leaderboard</a>
    </div>
</div>

{{-- Stats cards --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-100 p-5 text-center">
        <div class="text-3xl font-bold text-[#4A5D4A]">{{ $registrationCount }}</div>
        <div class="text-sm text-gray-500 mt-1">Registrations</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-5 text-center">
        <div class="text-3xl font-bold text-blue-600">{{ $attemptCount }}</div>
        <div class="text-sm text-gray-500 mt-1">Submitted Attempts</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-5 text-center">
        <div class="text-3xl font-bold text-green-600">NPR {{ number_format($competition->prize_pool) }}</div>
        <div class="text-sm text-gray-500 mt-1">Prize Pool</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-5 text-center">
        <div class="text-3xl font-bold text-gray-700">{{ $competition->mockTest->duration_minutes }}m</div>
        <div class="text-sm text-gray-500 mt-1">Test Duration</div>
    </div>
</div>

<div class="grid grid-cols-3 gap-6">
    {{-- Competition details --}}
    <div class="col-span-1 space-y-4">
        <div class="bg-white rounded-xl border border-gray-100 p-5 text-sm space-y-3">
            <h3 class="font-semibold text-gray-800 border-b pb-2">Details</h3>
            <div class="flex justify-between"><span class="text-gray-500">Category</span><span class="font-medium">{{ $competition->category?->name_en }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Mock Test</span><span class="font-medium">{{ $competition->mockTest?->title }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Questions</span><span class="font-medium">{{ $competition->mockTest?->question_count }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Entry Fee</span><span class="font-medium">{{ $competition->is_free ? 'Free' : 'NPR '.$competition->entry_fee }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Max Seats</span><span class="font-medium">{{ $competition->max_participants ?? 'Unlimited' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Starts</span><span class="font-medium">{{ $competition->starts_at->format('d M Y, h:i A') }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Ends</span><span class="font-medium">{{ $competition->ends_at->format('d M Y, h:i A') }}</span></div>
            @if($competition->winners_announced_at)
                <div class="flex justify-between"><span class="text-gray-500">Winners At</span><span class="font-medium text-purple-600">{{ $competition->winners_announced_at->format('d M Y') }}</span></div>
            @endif
        </div>

        {{-- Action buttons --}}
        @if(in_array($competition->status, ['ongoing','completed']))
        <div class="bg-white rounded-xl border border-gray-100 p-5 space-y-3">
            <h3 class="font-semibold text-gray-800 border-b pb-2">Actions</h3>
            <form action="{{ route('admin.learning.competitions.compute-leaderboard', $competition) }}" method="POST">
                @csrf
                <button type="submit"
                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                    🔢 Compute Leaderboard
                </button>
            </form>
            @if(!$competition->winners_announced_at)
            <form action="{{ route('admin.learning.competitions.announce-winners', $competition) }}" method="POST"
                  onsubmit="return confirm('Announce winners and send push notifications to all participants?')">
                @csrf
                <button type="submit"
                        class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition">
                    🏆 Announce Winners
                </button>
            </form>
            @endif
            <a href="{{ route('admin.learning.competitions.export', $competition) }}"
               class="block w-full text-center px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">
                📥 Export Results (CSV)
            </a>
        </div>
        @endif
    </div>

    {{-- Top attempts --}}
    <div class="col-span-2 bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 font-semibold text-gray-800">Top 10 Attempts</div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="py-2 px-4 text-left">Participant</th>
                    <th class="py-2 px-4 text-center">Score</th>
                    <th class="py-2 px-4 text-center">Correct</th>
                    <th class="py-2 px-4 text-center">Time</th>
                    <th class="py-2 px-4 text-center">Rank</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($topAttempts as $i => $attempt)
                <tr class="hover:bg-gray-50 {{ $i < 3 ? 'bg-yellow-50' : '' }}">
                    <td class="py-2 px-4">
                        <div class="flex items-center gap-2">
                            @if($i < 3)
                                <span class="text-lg">{{ ['🥇','🥈','🥉'][$i] }}</span>
                            @else
                                <span class="w-6 h-6 flex items-center justify-center bg-gray-100 rounded-full text-xs text-gray-500">{{ $i+1 }}</span>
                            @endif
                            <div>
                                <div class="font-medium text-gray-800">{{ $attempt->user?->name ?? 'Deleted' }}</div>
                                <div class="text-xs text-gray-400">{{ $attempt->user?->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-2 px-4 text-center font-bold text-[#4A5D4A]">{{ $attempt->score_percentage }}%</td>
                    <td class="py-2 px-4 text-center text-sm text-gray-600">
                        <span class="text-green-600">{{ $attempt->correct_answers }}✓</span>
                        <span class="text-red-500 ml-1">{{ $attempt->wrong_answers }}✗</span>
                    </td>
                    <td class="py-2 px-4 text-center text-xs text-gray-500">
                        {{ $attempt->time_taken_seconds ? gmdate('i:s', $attempt->time_taken_seconds) : '—' }}
                    </td>
                    <td class="py-2 px-4 text-center">
                        @if($attempt->rank)
                            <span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">#{{ $attempt->rank }}</span>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-6 text-center text-gray-400">No attempts yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Prize distribution --}}
@if($competition->prize_distribution)
<div class="mt-6 bg-white rounded-xl border border-gray-100 p-5">
    <h3 class="font-semibold text-gray-800 border-b pb-2 mb-4">Prize Distribution</h3>
    <div class="flex gap-4 flex-wrap">
        @foreach($competition->prize_distribution as $prize)
        <div class="flex items-center gap-3 bg-gradient-to-r from-yellow-50 to-amber-50 border border-amber-200 rounded-xl px-5 py-3">
            <span class="text-2xl">{{ ['🥇','🥈','🥉'][($prize['rank']-1)] ?? '🏅' }}</span>
            <div>
                <div class="font-bold text-gray-800">{{ $prize['label'] ?? 'Rank #'.$prize['rank'] }}</div>
                <div class="text-green-700 font-semibold text-sm">NPR {{ number_format($prize['amount']) }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection
