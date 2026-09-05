@extends('admin.layouts.app')
@section('title', 'Live Sessions')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Live Sessions</h2>
        <p class="text-sm text-gray-500 mt-1">Schedule live classes, recorded videos, and doubt clearing sessions.</p>
    </div>
    <a href="{{ route('admin.learning.live-sessions.create') }}"
       class="px-4 py-2 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] font-medium text-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Schedule Session
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 border-b text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">Title</th>
                <th class="py-3 px-4">Type</th>
                <th class="py-3 px-4">Category</th>
                <th class="py-3 px-4">Instructor</th>
                <th class="py-3 px-4">Starts At</th>
                <th class="py-3 px-4 text-center">Duration</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($sessions as $s)
            @php
                $typeMap    = ['live'=>'🔴 Live','recorded'=>'📹 Recorded','doubt_clearing'=>'❓ Doubt'];
                $statusColors = ['scheduled'=>'bg-blue-100 text-blue-700','live'=>'bg-red-100 text-red-700','ended'=>'bg-gray-100 text-gray-600','cancelled'=>'bg-orange-100 text-orange-700'];
                $sc = $statusColors[$s->status] ?? 'bg-gray-100 text-gray-600';
            @endphp
            <tr class="hover:bg-gray-50 {{ $s->status === 'live' ? 'bg-red-50' : '' }}">
                <td class="py-3 px-4">
                    <div class="font-semibold text-gray-900">{{ Str::limit($s->title_en, 45) }}</div>
                    @if($s->title_np)<div class="text-xs text-gray-400">{{ Str::limit($s->title_np, 40) }}</div>@endif
                </td>
                <td class="py-3 px-4 text-sm">{{ $typeMap[$s->type] ?? $s->type }}</td>
                <td class="py-3 px-4">
                    @if($s->category)
                        <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full text-xs">{{ $s->category->icon }} {{ $s->category->name_en }}</span>
                    @else
                        <span class="text-gray-400 text-xs">Global</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-xs text-gray-600">{{ $s->instructor_name ?? '—' }}</td>
                <td class="py-3 px-4 text-xs text-gray-700">{{ $s->starts_at->format('d M Y, h:i A') }}</td>
                <td class="py-3 px-4 text-center text-xs text-gray-600">{{ $s->duration_minutes }}m</td>
                <td class="py-3 px-4 text-center">
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc }}">{{ ucfirst($s->status) }}</span>
                </td>
                <td class="py-3 px-4 text-right space-x-2">
                    <a href="{{ route('admin.learning.live-sessions.edit', $s) }}" class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                    <form action="{{ route('admin.learning.live-sessions.destroy', $s) }}" method="POST" class="inline"
                          onsubmit="return confirm('Delete this session?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Del</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="py-10 text-center text-gray-400">No sessions scheduled yet. <a href="{{ route('admin.learning.live-sessions.create') }}" class="text-blue-600 hover:underline">Create one</a>.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t">{{ $sessions->links() }}</div>
</div>
@endsection
