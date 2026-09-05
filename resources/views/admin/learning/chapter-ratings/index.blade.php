@extends('admin.layouts.app')

@section('title', 'Chapter Ratings')
@section('subtitle', 'All student ratings and comments on study chapters')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Chapter Ratings</h2>
        <p class="text-sm text-gray-500 mt-1">Student feedback on study material chapters.</p>
    </div>
    <a href="{{ route('admin.learning.chapter-ratings.summary') }}"
       class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">
        📊 Chapter Summary
    </a>
</div>

{{-- Stats --}}
<div class="grid grid-cols-3 gap-5 mb-7">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
        <div class="text-3xl font-bold text-yellow-500">
            {{ number_format($avgRating, 1) }} ⭐
        </div>
        <div class="text-sm text-gray-500 mt-1">Overall Avg Rating</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
        <div class="text-3xl font-bold text-gray-800">{{ number_format($totalRatings) }}</div>
        <div class="text-sm text-gray-500 mt-1">Total Ratings</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <p class="text-xs font-semibold text-gray-600 mb-2">Distribution</p>
        @foreach([5,4,3,2,1] as $star)
        @php $cnt = $dist[$star] ?? 0; $pct = $totalRatings > 0 ? ($cnt / $totalRatings * 100) : 0; @endphp
        <div class="flex items-center gap-2 text-xs mb-1">
            <span class="w-6 text-right text-gray-600">{{ $star }}★</span>
            <div class="flex-1 bg-gray-100 rounded-full h-2">
                <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $pct }}%"></div>
            </div>
            <span class="w-8 text-gray-500">{{ $cnt }}</span>
        </div>
        @endforeach
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="flex flex-wrap gap-3 mb-5" id="filter-form">
    <select name="category_id" id="cat_select"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]"
            onchange="document.getElementById('chapter_select').value=''; this.form.submit()">
        <option value="">All Categories</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                {{ $cat->icon }} {{ $cat->name_en }}
            </option>
        @endforeach
    </select>

    @if($chapters->count())
    <select name="chapter_id" id="chapter_select" onchange="this.form.submit()"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
        <option value="">All Chapters</option>
        @foreach($chapters as $ch)
            <option value="{{ $ch->id }}" {{ request('chapter_id') == $ch->id ? 'selected' : '' }}>
                {{ $ch->title_en }}
            </option>
        @endforeach
    </select>
    @endif

    <select name="rating" onchange="this.form.submit()"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
        <option value="">All Stars</option>
        @foreach([5,4,3,2,1] as $s)
            <option value="{{ $s }}" {{ request('rating') == $s ? 'selected' : '' }}>{{ $s }} ★</option>
        @endforeach
    </select>

    @if(request()->hasAny(['category_id','chapter_id','rating']))
        <a href="{{ route('admin.learning.chapter-ratings.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">Clear</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">Chapter</th>
                <th class="py-3 px-4">Category</th>
                <th class="py-3 px-4">Student</th>
                <th class="py-3 px-4 text-center">Rating</th>
                <th class="py-3 px-4">Comment</th>
                <th class="py-3 px-4">Date</th>
                <th class="py-3 px-4 text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($ratings as $r)
            <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4 max-w-xs">
                    <div class="font-medium text-gray-900 truncate">{{ $r->chapter?->title_en ?? '—' }}</div>
                </td>
                <td class="py-3 px-4">
                    @if($r->chapter?->category)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                            {{ $r->chapter->category->icon }} {{ $r->chapter->category->name_en }}
                        </span>
                    @else
                        <span class="text-gray-400 text-xs">—</span>
                    @endif
                </td>
                <td class="py-3 px-4">
                    <div class="font-medium text-gray-800 text-xs">{{ $r->user?->name ?? '—' }}</div>
                    <div class="text-xs text-gray-400">{{ $r->user?->email }}</div>
                </td>
                <td class="py-3 px-4 text-center">
                    <span class="text-lg">
                        @for($i = 1; $i <= 5; $i++)
                            {{ $i <= $r->rating ? '⭐' : '☆' }}
                        @endfor
                    </span>
                    <div class="text-xs text-gray-500">{{ $r->rating }}/5</div>
                </td>
                <td class="py-3 px-4 max-w-xs">
                    @if($r->comment)
                        <p class="text-xs text-gray-700 line-clamp-2">{{ $r->comment }}</p>
                    @else
                        <span class="text-gray-300 text-xs">No comment</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-xs text-gray-400">{{ $r->created_at->format('d M Y') }}</td>
                <td class="py-3 px-4 text-right">
                    <form action="{{ route('admin.learning.chapter-ratings.destroy', $r) }}" method="POST"
                          class="inline" onsubmit="return confirm('Remove this rating?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-xs">Remove</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-10 text-center text-gray-400">No ratings found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">{{ $ratings->links() }}</div>
</div>
@endsection
