@extends('admin.layouts.app')

@section('title', 'Competitions')
@section('subtitle', 'Manage prize pool competitions across all learning categories')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Competitions</h2>
        <p class="text-sm text-gray-500 mt-1">Timed competitive exams with leaderboards and prize pools.</p>
    </div>
    <a href="{{ route('admin.learning.competitions.create') }}"
       class="px-4 py-2 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] transition font-medium text-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Create Competition
    </a>
</div>

{{-- Filters --}}
<form method="GET" class="flex gap-3 mb-5">
    <select name="status" onchange="this.form.submit()"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
        <option value="">All Statuses</option>
        <option value="draft"     {{ request('status') == 'draft'     ? 'selected' : '' }}>Draft</option>
        <option value="open"      {{ request('status') == 'open'      ? 'selected' : '' }}>Open</option>
        <option value="ongoing"   {{ request('status') == 'ongoing'   ? 'selected' : '' }}>Ongoing</option>
        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
    </select>
    <select name="category_id" onchange="this.form.submit()"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
        <option value="">All Categories</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                {{ $cat->icon }} {{ $cat->name_en }}
            </option>
        @endforeach
    </select>
    @if(request()->hasAny(['status', 'category_id']))
        <a href="{{ route('admin.learning.competitions.index') }}"
           class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">Clear</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">Title</th>
                <th class="py-3 px-4">Category</th>
                <th class="py-3 px-4">Period</th>
                <th class="py-3 px-4 text-center">Participants</th>
                <th class="py-3 px-4 text-right">Prize Pool</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($competitions as $comp)
            @php
                $statusColors = [
                    'draft'     => 'bg-gray-100 text-gray-600',
                    'open'      => 'bg-blue-100 text-blue-700',
                    'ongoing'   => 'bg-green-100 text-green-700',
                    'completed' => 'bg-purple-100 text-purple-700',
                    'cancelled' => 'bg-red-100 text-red-600',
                ];
                $color = $statusColors[$comp->status] ?? 'bg-gray-100 text-gray-600';
            @endphp
            <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4">
                    <div class="font-semibold text-gray-900">{{ Str::limit($comp->title, 45) }}</div>
                    @if($comp->title_np)
                        <div class="text-xs text-gray-400">{{ Str::limit($comp->title_np, 45) }}</div>
                    @endif
                </td>
                <td class="py-3 px-4">
                    @if($comp->category)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full text-xs">
                            {{ $comp->category->icon }} {{ $comp->category->name_en }}
                        </span>
                    @endif
                </td>
                <td class="py-3 px-4 text-xs text-gray-600">
                    <div>{{ $comp->starts_at->format('d M Y, h:i A') }}</div>
                    <div class="text-gray-400">→ {{ $comp->ends_at->format('d M Y, h:i A') }}</div>
                </td>
                <td class="py-3 px-4 text-center">
                    <a href="{{ route('admin.learning.competitions.registrations', $comp) }}"
                       class="text-blue-600 hover:text-blue-800 font-semibold">
                        {{ $comp->registrations()->where('payment_status','paid')->count() }}
                        @if($comp->max_participants)
                            <span class="text-gray-400 font-normal">/ {{ $comp->max_participants }}</span>
                        @endif
                    </a>
                </td>
                <td class="py-3 px-4 text-right font-semibold text-green-700">
                    @if($comp->prize_pool > 0)
                        NPR {{ number_format($comp->prize_pool) }}
                    @else
                        <span class="text-gray-400 font-normal">Free</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-center">
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $color }}">
                        {{ ucfirst($comp->status) }}
                    </span>
                </td>
                <td class="py-3 px-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.learning.competitions.show', $comp) }}"
                           class="text-gray-500 hover:text-gray-700 text-xs font-medium">View</a>
                        <a href="{{ route('admin.learning.competitions.leaderboard', $comp) }}"
                           class="text-purple-600 hover:text-purple-800 text-xs font-medium">Board</a>
                        <a href="{{ route('admin.learning.competitions.edit', $comp) }}"
                           class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                        <form action="{{ route('admin.learning.competitions.destroy', $comp) }}" method="POST"
                              class="inline" onsubmit="return confirm('Delete this competition?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Del</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-10 text-center text-gray-400">
                    No competitions yet. <a href="{{ route('admin.learning.competitions.create') }}" class="text-blue-600 hover:underline">Create one</a>.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">{{ $competitions->links() }}</div>
</div>
@endsection
