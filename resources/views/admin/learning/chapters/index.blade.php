@extends('admin.layouts.app')

@section('title', 'Learning Chapters')
@section('subtitle', 'Study material for all categories')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Study Chapters</h2>
        <p class="text-sm text-gray-500 mt-1">Manage all study material across Learning Center categories.</p>
    </div>
    <a href="{{ route('admin.learning.chapters.create') }}"
       class="px-4 py-2 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] transition font-medium text-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Chapter
    </a>
</div>

{{-- Filters --}}
<form method="GET" class="flex gap-3 mb-5">
    <select name="category_id" onchange="this.form.submit()"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
        <option value="">All Categories</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                {{ $cat->icon }} {{ $cat->name_en }}
            </option>
        @endforeach
    </select>
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search chapters..."
           class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] w-64">
    <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">Search</button>
    @if(request()->hasAny(['category_id', 'search']))
        <a href="{{ route('admin.learning.chapters.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">Clear</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">Order</th>
                <th class="py-3 px-4">Title (EN / NP)</th>
                <th class="py-3 px-4">Category</th>
                <th class="py-3 px-4 text-center">Read Time</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4">Published</th>
                <th class="py-3 px-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($chapters as $chapter)
            <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4 text-gray-400 font-mono text-xs">{{ $chapter->display_order }}</td>
                <td class="py-3 px-4 max-w-xs">
                    <div class="font-semibold text-gray-900 truncate">{{ $chapter->title_en }}</div>
                    <div class="text-xs text-gray-500 truncate">{{ $chapter->title_np }}</div>
                </td>
                <td class="py-3 px-4">
                    @if($chapter->category)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                            {{ $chapter->category->icon }} {{ $chapter->category->name_en }}
                        </span>
                    @endif
                </td>
                <td class="py-3 px-4 text-center text-xs text-gray-500">{{ $chapter->read_time_minutes }} min</td>
                <td class="py-3 px-4 text-center">
                    @if($chapter->is_published)
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Draft</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-xs text-gray-500">
                    {{ $chapter->published_at?->format('d M Y') ?? '—' }}
                </td>
                <td class="py-3 px-4 text-right space-x-3">
                    <a href="{{ route('admin.learning.chapters.edit', $chapter) }}"
                       class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                    <form action="{{ route('admin.learning.chapters.destroy', $chapter) }}" method="POST"
                          class="inline" onsubmit="return confirm('Delete this chapter?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-10 text-center text-gray-400">
                    No chapters yet. <a href="{{ route('admin.learning.chapters.create') }}" class="text-blue-600 hover:underline">Add one</a>.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">{{ $chapters->links() }}</div>
</div>
@endsection
