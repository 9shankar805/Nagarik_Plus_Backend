@extends('admin.layouts.app')

@section('title', 'Student Doubts')
@section('subtitle', 'Manage and answer student questions')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Student Doubts</h2>
        <p class="text-sm text-gray-500 mt-1">Review, answer, and moderate student questions.</p>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 gap-5 mb-7">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center text-2xl">❓</div>
        <div>
            <div class="text-2xl font-bold text-yellow-600">{{ $totalOpen }}</div>
            <div class="text-sm text-gray-500">Open / Unanswered</div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-2xl">✅</div>
        <div>
            <div class="text-2xl font-bold text-green-600">{{ $totalAnswered }}</div>
            <div class="text-sm text-gray-500">Answered</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="flex flex-wrap gap-3 mb-5">
    <select name="status" onchange="this.form.submit()"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
        <option value="">All Status</option>
        <option value="open"     {{ request('status') == 'open'     ? 'selected' : '' }}>Open</option>
        <option value="answered" {{ request('status') == 'answered' ? 'selected' : '' }}>Answered</option>
        <option value="closed"   {{ request('status') == 'closed'   ? 'selected' : '' }}>Closed</option>
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
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title or body..."
           class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] w-64">
    <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">Search</button>
    @if(request()->hasAny(['status','category_id','search']))
        <a href="{{ route('admin.learning.doubts.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">Clear</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">Question</th>
                <th class="py-3 px-4">Student</th>
                <th class="py-3 px-4">Category</th>
                <th class="py-3 px-4 text-center">Answers</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4 text-center">Pinned</th>
                <th class="py-3 px-4">Asked</th>
                <th class="py-3 px-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($doubts as $doubt)
            <tr class="hover:bg-gray-50 transition {{ $doubt->is_pinned ? 'bg-yellow-50' : '' }}">
                <td class="py-3 px-4 max-w-xs">
                    <a href="{{ route('admin.learning.doubts.show', $doubt) }}"
                       class="font-semibold text-gray-900 hover:text-blue-600 line-clamp-2">
                        {{ $doubt->title }}
                    </a>
                    @if($doubt->body)
                        <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ Str::limit($doubt->body, 80) }}</p>
                    @endif
                </td>
                <td class="py-3 px-4">
                    <div class="font-medium text-gray-800 text-xs">{{ $doubt->user?->name ?? '—' }}</div>
                    <div class="text-xs text-gray-400">{{ $doubt->user?->email }}</div>
                </td>
                <td class="py-3 px-4">
                    @if($doubt->category)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                            {{ $doubt->category->icon }} {{ $doubt->category->name_en }}
                        </span>
                    @else
                        <span class="text-gray-400 text-xs">—</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-center">
                    <span class="font-mono text-xs {{ $doubt->answers_count > 0 ? 'text-green-600 font-bold' : 'text-gray-400' }}">
                        {{ $doubt->answers_count }}
                    </span>
                </td>
                <td class="py-3 px-4 text-center">
                    @if($doubt->status === 'open')
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Open</span>
                    @elseif($doubt->status === 'answered')
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Answered</span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-500">Closed</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-center">
                    {{ $doubt->is_pinned ? '📌' : '—' }}
                </td>
                <td class="py-3 px-4 text-xs text-gray-400">{{ $doubt->created_at->format('d M Y') }}</td>
                <td class="py-3 px-4 text-right space-x-2">
                    <a href="{{ route('admin.learning.doubts.show', $doubt) }}"
                       class="text-blue-600 hover:text-blue-800 font-medium text-xs">Answer</a>
                    <form action="{{ route('admin.learning.doubts.destroy', $doubt) }}" method="POST"
                          class="inline" onsubmit="return confirm('Delete this doubt and all its answers?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-xs">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="py-10 text-center text-gray-400">No doubts found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">{{ $doubts->links() }}</div>
</div>
@endsection
