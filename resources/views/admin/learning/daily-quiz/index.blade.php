@extends('admin.layouts.app')
@section('title', 'Daily Quiz Schedule')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Daily Quiz</h2>
        <p class="text-sm text-gray-500 mt-1">Schedule one question per day per category — builds user streaks.</p>
    </div>
    <a href="{{ route('admin.learning.daily-quiz.create') }}"
       class="px-4 py-2 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] font-medium text-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Schedule Quiz
    </a>
</div>

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
        <a href="{{ route('admin.learning.daily-quiz.index') }}" class="px-3 py-2 text-gray-500 text-sm">Clear</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 border-b text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">Date</th>
                <th class="py-3 px-4">Question (preview)</th>
                <th class="py-3 px-4">Category</th>
                <th class="py-3 px-4 text-center">Difficulty</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($quizzes as $quiz)
            @php $isToday = $quiz->quiz_date->toDateString() === $today; @endphp
            <tr class="hover:bg-gray-50 {{ $isToday ? 'bg-yellow-50' : '' }}">
                <td class="py-3 px-4 font-semibold text-gray-900 whitespace-nowrap">
                    {{ $quiz->quiz_date->format('d M Y') }}
                    @if($isToday)<span class="ml-1 text-xs bg-yellow-400 text-yellow-900 px-2 py-0.5 rounded-full font-bold">TODAY</span>@endif
                </td>
                <td class="py-3 px-4 max-w-xs">
                    <p class="truncate text-gray-700">{{ $quiz->question?->question }}</p>
                </td>
                <td class="py-3 px-4">
                    @if($quiz->category)
                        <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full text-xs">{{ $quiz->category->icon }} {{ $quiz->category->name_en }}</span>
                    @else
                        <span class="text-gray-400 text-xs">Global</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-center">
                    @php $d = $quiz->question?->difficulty; @endphp
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $d === 'easy' ? 'bg-green-100 text-green-700' : ($d === 'hard' ? 'bg-red-100 text-red-600' : 'bg-orange-100 text-orange-700') }}">
                        {{ ucfirst($d ?? '—') }}
                    </span>
                </td>
                <td class="py-3 px-4 text-center">
                    @if($quiz->is_active)
                        <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Active</span>
                    @else
                        <span class="px-2.5 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-semibold">Inactive</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-right space-x-3">
                    <a href="{{ route('admin.learning.daily-quiz.stats', $quiz) }}" class="text-purple-600 hover:text-purple-800 font-medium">Stats</a>
                    <a href="{{ route('admin.learning.daily-quiz.edit', $quiz) }}" class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                    <form action="{{ route('admin.learning.daily-quiz.destroy', $quiz) }}" method="POST" class="inline"
                          onsubmit="return confirm('Delete this daily quiz?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Del</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="py-10 text-center text-gray-400">No daily quizzes scheduled yet. <a href="{{ route('admin.learning.daily-quiz.create') }}" class="text-blue-600 hover:underline">Schedule one</a>.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t">{{ $quizzes->links() }}</div>
</div>
@endsection
