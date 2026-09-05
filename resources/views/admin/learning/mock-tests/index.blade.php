@extends('admin.layouts.app')

@section('title', 'Mock Tests')
@section('subtitle', 'Manage timed mock tests for all learning categories')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Mock Tests</h2>
        <p class="text-sm text-gray-500 mt-1">Timed, scored tests with optional negative marking.</p>
    </div>
    <a href="{{ route('admin.learning.mock-tests.create') }}"
       class="px-4 py-2 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] transition font-medium text-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Mock Test
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
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search mock tests..."
           class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] w-64">
    <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">Search</button>
    @if(request()->hasAny(['category_id', 'search']))
        <a href="{{ route('admin.learning.mock-tests.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">Clear</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">Title</th>
                <th class="py-3 px-4">Category</th>
                <th class="py-3 px-4 text-center">Questions</th>
                <th class="py-3 px-4 text-center">Duration</th>
                <th class="py-3 px-4 text-center">Pass %</th>
                <th class="py-3 px-4 text-center">Negative</th>
                <th class="py-3 px-4 text-center">Featured</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($mockTests as $test)
            <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4">
                    <div class="font-semibold text-gray-900">{{ $test->title }}</div>
                    @if($test->title_np)
                        <div class="text-xs text-gray-500">{{ $test->title_np }}</div>
                    @endif
                </td>
                <td class="py-3 px-4">
                    @if($test->learningCategory)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                            {{ $test->learningCategory->icon }} {{ $test->learningCategory->name_en }}
                        </span>
                    @else
                        <span class="text-gray-400 text-xs">{{ $test->category }}</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-center font-mono text-xs">{{ $test->question_count }}</td>
                <td class="py-3 px-4 text-center text-xs text-gray-600">{{ $test->duration_minutes }} min</td>
                <td class="py-3 px-4 text-center text-xs text-gray-600">{{ $test->pass_percentage }}%</td>
                <td class="py-3 px-4 text-center">
                    @if($test->negative_marking)
                        <span class="px-2 py-0.5 bg-red-50 text-red-700 rounded-full text-xs font-medium">-{{ $test->negative_value }}</span>
                    @else
                        <span class="text-gray-300 text-xs">—</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-center">
                    @if($test->is_featured)
                        <span class="text-yellow-500 text-lg" title="Featured">⭐</span>
                    @else
                        <span class="text-gray-300">—</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-center">
                    @if($test->is_active)
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-500">Inactive</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-right space-x-3">
                    <a href="{{ route('admin.learning.mock-tests.show', $test) }}"
                       class="text-gray-500 hover:text-gray-800 font-medium text-xs">Stats</a>
                    <a href="{{ route('admin.learning.mock-tests.edit', $test) }}"
                       class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                    <form action="{{ route('admin.learning.mock-tests.destroy', $test) }}" method="POST"
                          class="inline" onsubmit="return confirm('Delete this mock test?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="py-10 text-center text-gray-400">
                    No mock tests yet. <a href="{{ route('admin.learning.mock-tests.create') }}" class="text-blue-600 hover:underline">Create one</a>.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">{{ $mockTests->links() }}</div>
</div>
@endsection
