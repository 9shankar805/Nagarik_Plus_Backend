@extends('admin.layouts.app')

@section('title', 'Learning Categories')
@section('subtitle', 'Manage sections of the Learning Center')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Learning Categories</h2>
        <p class="text-sm text-gray-500 mt-1">Driving, Loksewa, Finance, Rights, and other exam sections.</p>
    </div>
    <a href="{{ route('admin.learning.categories.create') }}"
       class="px-4 py-2 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] transition font-medium text-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Category
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">Order</th>
                <th class="py-3 px-4">Icon</th>
                <th class="py-3 px-4">Name (EN / NP)</th>
                <th class="py-3 px-4">Slug</th>
                <th class="py-3 px-4 text-center">Chapters</th>
                <th class="py-3 px-4 text-center">Mock Tests</th>
                <th class="py-3 px-4 text-center">Competitions</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($categories as $cat)
            <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4 text-gray-500 font-mono text-xs">{{ $cat->display_order }}</td>
                <td class="py-3 px-4 text-2xl">{{ $cat->icon ?? '📚' }}</td>
                <td class="py-3 px-4">
                    <div class="font-semibold text-gray-900">{{ $cat->name_en }}</div>
                    <div class="text-xs text-gray-500">{{ $cat->name_np }}</div>
                </td>
                <td class="py-3 px-4 font-mono text-xs text-gray-500">{{ $cat->slug }}</td>
                <td class="py-3 px-4 text-center">
                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full text-xs font-semibold">{{ $cat->chapters_count }}</span>
                </td>
                <td class="py-3 px-4 text-center">
                    <span class="px-2 py-0.5 bg-purple-50 text-purple-700 rounded-full text-xs font-semibold">{{ $cat->mock_tests_count }}</span>
                </td>
                <td class="py-3 px-4 text-center">
                    <span class="px-2 py-0.5 bg-orange-50 text-orange-700 rounded-full text-xs font-semibold">{{ $cat->competitions_count }}</span>
                </td>
                <td class="py-3 px-4 text-center">
                    @if($cat->is_active)
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Inactive</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-right space-x-3">
                    <a href="{{ route('admin.learning.categories.edit', $cat) }}"
                       class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                    <form action="{{ route('admin.learning.categories.destroy', $cat) }}" method="POST" class="inline"
                          onsubmit="return confirm('Delete \'{{ $cat->name_en }}\' and all its chapters? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="py-10 text-center text-gray-400">
                    No categories yet. <a href="{{ route('admin.learning.categories.create') }}" class="text-blue-600 hover:underline">Add one</a>.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">{{ $categories->links() }}</div>
</div>
@endsection
