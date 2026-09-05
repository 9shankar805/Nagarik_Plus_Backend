@extends('admin.layouts.app')
@section('title', 'Flashcard Sets')
@section('subtitle', 'Manage flashcard sets for spaced repetition learning')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Flashcard Sets</h2>
        <p class="text-sm text-gray-500 mt-1">Each set contains front/back cards for term-definition study.</p>
    </div>
    <a href="{{ route('admin.learning.flashcards.create') }}"
       class="px-4 py-2 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] transition font-medium text-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Set
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
        <a href="{{ route('admin.learning.flashcards.index') }}" class="px-3 py-2 text-gray-500 text-sm">Clear</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 border-b text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">Order</th>
                <th class="py-3 px-4">Set Title</th>
                <th class="py-3 px-4">Category</th>
                <th class="py-3 px-4 text-center">Cards</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($sets as $set)
            <tr class="hover:bg-gray-50">
                <td class="py-3 px-4 text-gray-400 font-mono text-xs">{{ $set->display_order }}</td>
                <td class="py-3 px-4">
                    <div class="font-semibold text-gray-900">{{ $set->title_en }}</div>
                    @if($set->title_np)<div class="text-xs text-gray-400">{{ $set->title_np }}</div>@endif
                </td>
                <td class="py-3 px-4">
                    @if($set->category)
                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full text-xs">{{ $set->category->icon }} {{ $set->category->name_en }}</span>
                    @else<span class="text-gray-400 text-xs">—</span>@endif
                </td>
                <td class="py-3 px-4 text-center">
                    <span class="px-2.5 py-1 bg-purple-50 text-purple-700 rounded-full text-xs font-bold">{{ $set->card_count }}</span>
                </td>
                <td class="py-3 px-4 text-center">
                    @if($set->is_published)
                        <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Published</span>
                    @else
                        <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">Draft</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-right space-x-3">
                    <a href="{{ route('admin.learning.flashcards.edit', $set) }}" class="text-blue-600 hover:text-blue-800 font-medium">Edit / Cards</a>
                    <form action="{{ route('admin.learning.flashcards.destroy', $set) }}" method="POST" class="inline"
                          onsubmit="return confirm('Delete this set and all its cards?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="py-10 text-center text-gray-400">No flashcard sets yet. <a href="{{ route('admin.learning.flashcards.create') }}" class="text-blue-600 hover:underline">Create one</a>.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t">{{ $sets->links() }}</div>
</div>
@endsection
