@extends('admin.layouts.app')
@section('title', 'Achievements / Badges')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Achievements & Badges</h2>
        <p class="text-sm text-gray-500 mt-1">Auto-awarded to users when they meet the threshold conditions.</p>
    </div>
    <a href="{{ route('admin.learning.achievements.create') }}"
       class="px-4 py-2 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] font-medium text-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Badge
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 border-b text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">Badge</th>
                <th class="py-3 px-4">Title</th>
                <th class="py-3 px-4">Type</th>
                <th class="py-3 px-4 text-center">Threshold</th>
                <th class="py-3 px-4 text-center">Earned By</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($achievements as $a)
            @php
                $typeColors = ['streak'=>'bg-orange-100 text-orange-700','daily'=>'bg-yellow-100 text-yellow-700','quiz'=>'bg-blue-100 text-blue-700','test'=>'bg-green-100 text-green-700','chapter'=>'bg-purple-100 text-purple-700','competition'=>'bg-red-100 text-red-600','special'=>'bg-pink-100 text-pink-700'];
                $tc = $typeColors[$a->type] ?? 'bg-gray-100 text-gray-600';
            @endphp
            <tr class="hover:bg-gray-50">
                <td class="py-3 px-4">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">{{ $a->icon ?? '🏅' }}</span>
                        <span class="w-3 h-3 rounded-full inline-block" style="background: {{ $a->badge_color }}"></span>
                    </div>
                </td>
                <td class="py-3 px-4">
                    <div class="font-semibold text-gray-900">{{ $a->title_en }}</div>
                    <div class="text-xs text-gray-400 font-mono">{{ $a->slug }}</div>
                </td>
                <td class="py-3 px-4">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $tc }}">{{ ucfirst($a->type) }}</span>
                </td>
                <td class="py-3 px-4 text-center font-semibold text-gray-700">{{ $a->threshold }}</td>
                <td class="py-3 px-4 text-center">
                    <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs font-bold">{{ $a->user_achievements_count }}</span>
                </td>
                <td class="py-3 px-4 text-center">
                    @if($a->is_active)
                        <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Active</span>
                    @else
                        <span class="px-2.5 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-semibold">Inactive</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-right space-x-3">
                    <a href="{{ route('admin.learning.achievements.edit', $a) }}" class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                    <form action="{{ route('admin.learning.achievements.destroy', $a) }}" method="POST" class="inline"
                          onsubmit="return confirm('Delete this badge?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="py-10 text-center text-gray-400">No badges yet. <a href="{{ route('admin.learning.achievements.create') }}" class="text-blue-600 hover:underline">Create one</a>.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t">{{ $achievements->links() }}</div>
</div>
@endsection
