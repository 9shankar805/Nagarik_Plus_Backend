@extends('admin.layouts.app')

@section('title', 'Video Classes')
@section('subtitle', 'Manage recorded and live video lessons')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Video Classes</h2>
        <p class="text-sm text-gray-500 mt-1">Recorded VODs and scheduled live streams.</p>
    </div>
    <a href="{{ route('admin.learning.video-classes.create') }}"
       class="px-4 py-2 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] transition font-medium text-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Video Class
    </a>
</div>

{{-- Filters --}}
<form method="GET" class="flex flex-wrap gap-3 mb-5">
    <select name="category_id" onchange="this.form.submit()"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
        <option value="">All Categories</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                {{ $cat->icon }} {{ $cat->name_en }}
            </option>
        @endforeach
    </select>
    <select name="type" onchange="this.form.submit()"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
        <option value="">All Types</option>
        <option value="recorded" {{ request('type') == 'recorded' ? 'selected' : '' }}>Recorded</option>
        <option value="live"     {{ request('type') == 'live'     ? 'selected' : '' }}>Live</option>
    </select>
    <select name="status" onchange="this.form.submit()"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
        <option value="">All Status</option>
        <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>Active</option>
        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title..."
           class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] w-56">
    <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">Search</button>
    @if(request()->hasAny(['category_id','type','status','search']))
        <a href="{{ route('admin.learning.video-classes.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">Clear</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">Thumbnail</th>
                <th class="py-3 px-4">Title</th>
                <th class="py-3 px-4">Category</th>
                <th class="py-3 px-4 text-center">Type</th>
                <th class="py-3 px-4 text-center">Duration</th>
                <th class="py-3 px-4 text-center">Scheduled At</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($videos as $video)
            <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4">
                    @if($video->thumbnail_url)
                        <img src="{{ $video->thumbnail_url }}" alt="thumb" class="w-16 h-10 object-cover rounded-md border border-gray-200">
                    @else
                        <div class="w-16 h-10 bg-gray-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.868v6.264a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                </td>
                <td class="py-3 px-4">
                    <div class="font-semibold text-gray-900 max-w-xs truncate">{{ $video->title }}</div>
                    @if($video->description)
                        <div class="text-xs text-gray-400 truncate max-w-xs">{{ Str::limit($video->description, 60) }}</div>
                    @endif
                </td>
                <td class="py-3 px-4">
                    @if($video->category)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                            {{ $video->category->icon }} {{ $video->category->name_en }}
                        </span>
                    @else
                        <span class="text-gray-400 text-xs">—</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-center">
                    @if($video->is_live)
                        <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-semibold">🔴 Live</span>
                    @else
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">📹 Recorded</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-center text-xs text-gray-600">
                    {{ $video->duration_minutes ? $video->duration_minutes . ' min' : '—' }}
                </td>
                <td class="py-3 px-4 text-center text-xs text-gray-600">
                    {{ $video->live_scheduled_at ? $video->live_scheduled_at->format('d M Y, H:i') : '—' }}
                </td>
                <td class="py-3 px-4 text-center">
                    @if($video->status === 'active')
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-500">{{ ucfirst($video->status) }}</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-right space-x-3">
                    <a href="{{ $video->video_url }}" target="_blank"
                       class="text-purple-600 hover:text-purple-800 font-medium text-xs">Preview</a>
                    <a href="{{ route('admin.learning.video-classes.edit', $video) }}"
                       class="text-blue-600 hover:text-blue-800 font-medium text-xs">Edit</a>
                    <form action="{{ route('admin.learning.video-classes.destroy', $video) }}" method="POST"
                          class="inline" onsubmit="return confirm('Delete this video class?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-xs">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="py-10 text-center text-gray-400">
                    No video classes yet. <a href="{{ route('admin.learning.video-classes.create') }}" class="text-blue-600 hover:underline">Add one</a>.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">{{ $videos->links() }}</div>
</div>
@endsection
