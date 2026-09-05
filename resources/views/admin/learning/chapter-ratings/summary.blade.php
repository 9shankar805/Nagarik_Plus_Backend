@extends('admin.layouts.app')

@section('title', 'Chapter Rating Summary')
@section('subtitle', 'Average ratings per chapter, sorted by highest rated')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Chapter Rating Summary</h2>
        <p class="text-sm text-gray-500 mt-1">Average rating per chapter, based on student feedback.</p>
    </div>
    <a href="{{ route('admin.learning.chapter-ratings.index') }}"
       class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">
        ← All Ratings
    </a>
</div>

{{-- Category filter --}}
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
    @if(request('category_id'))
        <a href="{{ route('admin.learning.chapter-ratings.summary') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">Clear</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">Chapter</th>
                <th class="py-3 px-4">Category</th>
                <th class="py-3 px-4 text-center">Avg Rating</th>
                <th class="py-3 px-4 text-center">Total Ratings</th>
                <th class="py-3 px-4 text-right">View Ratings</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($chapters as $ch)
            @php $avg = round($ch->avg_rating ?? 0, 1); @endphp
            <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4 font-medium text-gray-900">{{ $ch->title_en }}</td>
                <td class="py-3 px-4">
                    @if($ch->category)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                            {{ $ch->category->icon }} {{ $ch->category->name_en }}
                        </span>
                    @else
                        <span class="text-gray-400 text-xs">—</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-center">
                    <div class="flex items-center justify-center gap-1">
                        <span class="font-bold text-lg
                            {{ $avg >= 4 ? 'text-green-600' : ($avg >= 3 ? 'text-yellow-600' : 'text-red-500') }}">
                            {{ $avg }}
                        </span>
                        <span class="text-yellow-400 text-base">⭐</span>
                    </div>
                    {{-- Visual bar --}}
                    <div class="w-24 mx-auto bg-gray-100 rounded-full h-1.5 mt-1">
                        <div class="h-1.5 rounded-full
                            {{ $avg >= 4 ? 'bg-green-500' : ($avg >= 3 ? 'bg-yellow-400' : 'bg-red-400') }}"
                             style="width: {{ ($avg / 5) * 100 }}%"></div>
                    </div>
                </td>
                <td class="py-3 px-4 text-center font-mono text-gray-700">{{ $ch->total_ratings }}</td>
                <td class="py-3 px-4 text-right">
                    <a href="{{ route('admin.learning.chapter-ratings.index', ['chapter_id' => $ch->id]) }}"
                       class="text-blue-600 hover:text-blue-800 font-medium text-xs">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-10 text-center text-gray-400">No chapters with ratings yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">{{ $chapters->links() }}</div>
</div>
@endsection
